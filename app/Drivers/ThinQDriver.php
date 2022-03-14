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

class ThinQDriver implements Driver
{
    private int $maxMessageLength = 910;
    private string $requestInputMessageKey = 'message';
    private string $requestInputUidKey = 'guid';
    private string $requestInputStatusKey = 'send_status';

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

        LogEvent::dispatch(
            "Delivery Notifications",
            get_class( $this ), 'info', json_encode([$arr['delivery_notifications']]), null
        );

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
}
