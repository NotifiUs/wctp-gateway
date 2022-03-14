<?php

namespace App\Drivers;

use Exception;
use App\Carrier;
use App\Message;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendThinqSMS;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\RequestException;

class ThinQDriver implements Driver
{
    private int $maxMessageLength = 910;
    private string $requestInputMessageKey = 'message';
    private string $requestInputUidKey = 'guid';
    private string $requestInputStatusKey = 'send_status';

    public function getType( string $identifier ): string
    {
        return "PN";
    }

    public function getFriendlyType( string $identifier ): string
    {
        return "Phone Number";
    }

    public function queueOutbound($host, $carrier, $recipient, $message, $messageID, $reply_with): void
    {
        SendThinqSMS::dispatch( $host, $carrier, $recipient, $message, $messageID, $reply_with );
    }

    public function getRequestInputStatusKey(): string
    {
        return $this->requestInputStatusKey;
    }

    public function getRequestInputUidKey(): string
    {
        return $this->requestInputUidKey;
    }

    public function getMaxMessageLength(): int
    {
        return $this->maxMessageLength;
    }

    public function getHandlerResponse(): Response
    {
        return response( json_encode([ 'success' => true ]), 200, ['content-type' => 'application/json']);
    }

    public function verifyHandlerRequest( Request $request, Carrier $carrier  ): bool
    {
        //https://apidocs.thinq.com/?version=latest#7c5909e8-596c-47b3-9f24-438196eef374
        //all requests come from 192.81.236.250
        if( $request->getClientIp() === '192.81.236.250')
        {
            return true;
        }

        LogEvent::dispatch(
            "Failed inbound message",
            get_class( $this ), 'error', json_encode("Request not from ThinQ documented IP address"), null
        );

        return false;
    }

    public function getRequestInputMessageKey(): string
    {
        return $this->requestInputMessageKey;
    }

    public function saveInboundMessage(Request $request, int $carrier_id, int $number_id, int $enterprise_host_id, Carbon $submitted_at, $reply_with = null): void
    {
        SaveMessage::dispatch(
            $carrier_id,
            $number_id,
            $enterprise_host_id,
            "+1{$request->input('to')}",
            "+1{$request->input('from')}",
            encrypt( $request->input($this->getRequestInputMessageKey()) ),
            null,
            $submitted_at,
            $reply_with,
            $request->header('X-sms-guid'),
            'inbound'
        );
    }

    public function updateMessageStatus(Carrier $carrier, Message $message): bool
    {
        /*
        * https://api.thinq.com/account/{{account_id}}/product/origination/sms/{{message_id}}
        */
        try{
            $thinq = new Guzzle([
                'timeout' => 10.0,
                'base_uri' => 'https://api.thinq.com',
                'auth' => [ $carrier->thinq_api_username, decrypt($carrier->thinq_api_token)],
            ]);
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failed decrypting carrier api token",
                get_class( $this ), 'error', json_encode($carrier->toArray()), null
            );
            return false;
        }

        try{
            $result = $thinq->get("account/{$carrier->thinq_account_id}/product/origination/sms/{$message->carrier_message_uid}");
        }
        catch( Exception $e )
        {
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );

            return false;
        }

        if( $result->getStatusCode() !== 200 )
        {
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode($result->getReasonPhrase()), null
            );
            return false;
        }

        $body = $result->getBody();
        $json = $body->getContents();
        $arr = json_decode( $json, true );
        if( ! isset( $arr['delivery_notifications']))
        {
            LogEvent::dispatch(
                "Failure getting delivery notifications",
                get_class( $this ), 'error', json_encode([$arr, $arr['delivery_notifications']]), null
            );
            return false;
        }

        $ts = null;
        $latest_update = null;

        foreach( $arr['delivery_notifications'] as $dn )
        {
            if( $ts === null || Carbon::parse( $dn['timestamp'] ) >= $ts )
            {
                $ts = Carbon::parse( $dn['timestamp']);
                $latest_update = $dn;
            }
        }

        if(  $latest_update !== null )
        {
            if($message->status !== $latest_update['send_status'])
            {
                // Only send notifications when there is a new update
                LogEvent::dispatch(
                    "Delivery Notifications Update",
                    get_class( $this ), 'info', json_encode([$message->toArray(), $arr['delivery_notifications']]), null
                );
            }

            switch( strtolower($latest_update['send_status']) ) {
                case "sent":
                case "delivrd":
                case "delivered":
                    $message->status = $latest_update['send_status'];
                    $message->delivered_at = Carbon::parse($latest_update['timestamp']);
                    break;
                case "rejectd":
                case "expired":
                case "deleted":
                case "unknown":
                case "failed":
                case "undelivered":
                case "undeliv":
                    $message->status = $latest_update['send_status'];
                    $message->failed_at = Carbon::parse($latest_update['timestamp']);
                    break;
                default:
                    break;
            }
        }

        try{
            $message->save();
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure updating message status",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
           return false;
        }

        return true;
    }

    public function provisionNumber(Carrier $carrier, string $identifier): bool
    {
        $ipify = new Guzzle(['base_uri' => 'https://api.ipify.org']);

        try{
            $response = $ipify->get( '/');
        }
        catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

        $ip = (string)$response->getBody();
        $validator = Validator::make(['ip' => $ip], ['ip' => 'required|ip']);

        if( $validator->fails() ) { Log::debug('IP validation failed' ); return false; }

        try{
            $thinq = new Guzzle([
                'base_uri' => 'https://api.thinq.com',
                'auth' => [ $carrier->thinq_api_username, decrypt($carrier->thinq_api_token)],
                'headers' => [ 'content-type' => 'application/json' ],
            ]);
        }
        catch( Exception $e ){ Log::debug($e->getMessage());return false; }

        //get all current ip whitelists
        $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/ip";

        try{
            $res = $thinq->get($url);
        }
        catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

        $hasIP = false;
        $list = json_decode( (string)$res->getBody(), true );
        foreach( $list['rows'] as $row )
        {
            if( $row['ip'] == $ip ){  $hasIP = true; break; }
        }

        //if our public ip is in the whitelist list, continue
        //if our public ip is not in the whitlelist list, add it
        if( ! $hasIP )
        {
            $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/ip/{$ip}";
            try{
                $res = $thinq->post($url);
            }
            catch( Exception $e ){ Log::debug($e->getMessage()); return false; }
        }

        //get all current sms routing profiles
        $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/profile";
        try{
            $res = $thinq->get($url);
        }
        catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

        $hasProfile = false;
        $list = json_decode( (string)$res->getBody(), true );
        $sms_routing_profile = '';

        foreach( $list['rows'] as $row )
        {
            if( $row['name'] == $identifier )
            {
                $sms_routing_profile = $row['id'];
                $hasProfile = true;
                break;
            }
        }

        //if our url is in the profile list, continue
        //if our url is not in the profile list, add it
        if( ! $hasProfile )
        {
            $webhook = secure_url("/sms/inbound/{$identifier}/primary" );
            $body = [
                "sms_routing_profile" => [
                    'name' => $identifier,
                    'url' => $webhook,
                    'attachment_type' => 'url'
                ]
            ];

            $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/profile";
            try{
                $res = $thinq->post($url, ['body' => json_encode( $body ) ]);
            }
            catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

            $profile = json_decode( (string)$res->getBody(), true );

            $sms_routing_profile = $profile['id'];
        }
        else
        {
            //update it so we enesure it has our most recent url
            $webhook = secure_url("/sms/inbound/{$identifier}/primary" );
            $body = [
                "sms_routing_profile" => [
                    'name' => $identifier,
                    'url' => $webhook,
                    'attachment_type' => 'url'
                ]
            ];

            $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/profile/{$sms_routing_profile}";
            try{
                $res = $thinq->put($url, ['body' => json_encode( $body ) ]);
            }
            catch( Exception $e ){ Log::debug($e->getMessage()); return false; }
        }

        //set our outbound message status url
        //update it so we enesure it has our most recent url
        $webhook = secure_url("/sms/callback/{$identifier}/status" );
        $body = [
            "settings" => [
                'deliveryConfirmationUrl' => $webhook,
                'deliveryNotificationType' => 'form-data',
            ]
        ];

        $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/settings/outbound";
        try{
            $res = $thinq->post($url, ['body' => json_encode( $body ) ]);
        }
        catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

        //create a feature order to do the following:
        //  enable SMS
        //  associate sms routing profile
        $body = [
            "order" => [
                "tns" => [
                    [
                        "sms_routing_profile_id" => $sms_routing_profile,
                        "features" => [ "cnam" => false, "e911" => false, "sms" => true ],
                        "did" => $identifier,
                    ]
                ]
            ]
        ];

        $url = "/account/{$carrier->thinq_account_id}/origination/did/features/create";
        try{
            $res = $thinq->post($url, ['body' => json_encode( $body ) ]);
        }
        catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

        $order = json_decode( (string)$res->getBody(), true );

        // complete feature order
        $url = "/account/{$carrier->thinq_account_id}/origination/did/features/complete/{$order['order']['id']}";
        try{
            $res = $thinq->post($url);
        }
        catch( Exception $e ){ Log::debug($e->getMessage()); return false; }

        return true;
    }

    public function getCarrierDetails(Carrier $carrier, string $identifier): array
    {
        $url = "/origination/did/search2/did/{$carrier->thinq_account_id}";
        $guzzle = new Guzzle(
            ['base_uri' => 'https://api.thinq.com',]
        );
        try{
            $res = $guzzle->get( $url, ['auth' => [ $carrier->thinq_api_username, decrypt($carrier->thinq_api_token)]]);
        }
        catch( RequestException $e ) {
            return [];
        }
        catch( Exception $e ){
            return [];
        }

        $thinq_numbers = json_decode( (string)$res->getBody(), true );

        if( $thinq_numbers['total_rows'] > 0 )
        {
            foreach( $thinq_numbers['rows'] as $thinq_number )
            {
                if( $identifier == $thinq_number['id'] )
                {
                    return $thinq_number;
                }
            }
        }

        return [];
    }
}
