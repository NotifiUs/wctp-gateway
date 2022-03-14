<?php

namespace App\Drivers;

use Exception;
use App\Message;
use App\Carrier;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Jobs\SendTwilioSMS;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twilio\Security\RequestValidator;
use Twilio\Rest\Client as TwilioClient;

class TwilioDriver implements Driver
{
    private int $maxMessageLength = 1600;
    private string $requestInputMessageKey = 'Body';
    private string $requestInputUidKey = 'MessageSid';
    private string $requestInputStatusKey = 'MessageStatus';

    public function getType( string $identifier ): string
    {
        return substr( $identifier, 0, 2);
    }

    public function getFriendlyType( string $identifier ): string
    {
        if( Str::startsWith( $identifier, 'MG') )
        {
            return "Messaging Service";
        }

        return "Phone Number";
    }

    public function queueOutbound($host, $carrier, $recipient, $message, $messageID, $reply_with): void
    {
        SendTwilioSMS::dispatch( $host, $carrier, $recipient, $message, $messageID, $reply_with );
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
        return response('<Response></Response>', 200, ['content-type' => 'application/xml']);
    }

    public function verifyHandlerRequest( Request $request, Carrier $carrier ): bool
    {
        try{
            $validator = new RequestValidator( decrypt( $carrier->twilio_auth_token )  );
        }
        catch( Exception $e )
        {
            LogEvent::dispatch(
                "Failed inbound message",
                get_class( $this ), 'error', json_encode("Unable to decrypt twilio auth token"), null
            );

            return false;
        }

        if( $validator->validate(
            $request->header('X-Twilio-Signature'),
            $request->fullUrl(),
            $request->all()
        ))
        {
            return true;
        }

        LogEvent::dispatch(
            "Failed inbound message",
            get_class( $this ), 'error', json_encode("Unable to verify Twilio request"), null
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
            $request->input('To'),
            $request->input('From'),
            encrypt( $request->input($this->getRequestInputMessageKey()) ),
            null,
            $submitted_at,
            $reply_with,
            $request->input('MessageSid'),
            'inbound'
        );
    }

    public function updateMessageStatus(Carrier $carrier, Message $message ): bool
    {
        //use twilio api to check status of message->carrier_uniquie_id
        try{
            $client = new TwilioClient(
                $carrier->twilio_account_sid,
                decrypt( $carrier->twilio_auth_token )
            );
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
            return false;
        }

        try{
            $carrier_message = $client->messages( $message->carrier_message_uid )->fetch();
            $message->status = $carrier_message->status;

            switch (strtolower($carrier_message->status)) {
                case "sent":
                case "delivrd":
                case "delivered":
                    $message->delivered_at = Carbon::now();
                    break;
                case "rejectd":
                case "expired":
                case "deleted":
                case "unknown":
                case "failed":
                case "undelivered":
                case "undeliv":
                default:
                    $message->failed_at = Carbon::now();
                    break;
            }

            $message->save();
        }
        catch( Exception $e ){
            LogEvent::dispatch(
                "Failure synchronizing message status",
                get_class( $this ), 'error', json_encode($e->getMessage()), null
            );
            return false;
        }

        return true;
    }

    public function provisionNumber(Carrier $carrier, $identifier): bool
    {
        try{
            $twilio = new TwilioClient( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );

            if( $this->getType($identifier) == 'MG' )
            {
                $twilio->messaging->v1->services($identifier)
                    ->update([
                        'inboundRequestUrl' => secure_url("/sms/inbound/{$identifier}/primary" ),
                        'inboundMethod' => 'POST',
                        'fallbackUrl' => secure_url("/sms/inbound/{$identifier}/fallback" ),
                        'fallbackMethod' => 'POST',
                        'statusCallback' => secure_url("/sms/callback/{$identifier}/status"),
                        //'statusCallbackMethod' => 'POST', //not in docs
                    ]);
            }
            else
            {
                $twilio
                    ->incomingPhoneNumbers( $identifier )
                    ->update(array(
                            'smsApplicationSid' => '',
                            'smsFallbackMethod' => 'POST',
                            'smsFallbackUrl' => secure_url("/sms/inbound/{$identifier}/fallback"),
                            'smsMethod' => 'POST',
                            'smsUrl' => secure_url("/sms/inbound/{$identifier}/primary" ),
                            'statusCallback' => secure_url("/sms/callback/{$identifier}/status"),
                            'statusCallbackMethod' => 'POST',
                        )
                    );
            }
        }
        catch( Exception $e ) { return false; }

        return true;
    }

    public function getCarrierDetails(Carrier $carrier, string $identifier ): array
    {
        try {
            $twilio = new TwilioClient( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );

            if( $this->getType($identifier) == 'MG' )
            {
                $serviceAddons = [];
                $results = $twilio->messaging->v1->services( $identifier )->fetch();
                foreach( $results->phoneNumbers->read(100, 100) as $num )
                {
                    $serviceAddons['numbers'][] = $num->toArray();
                }

                foreach( $results->shortCodes->read(100, 100) as $shortcode )
                {
                    $serviceAddons['shortcodes'][] = $shortcode->toArray();
                }
                return Arr::dot(array_merge( $results->toArray(), $serviceAddons ) );
            }
            else {

                $results = $twilio->incomingPhoneNumbers($identifier)->fetch();
                return Arr::dot($results->toArray() );
            }

        }catch( Exception $e ) { return []; }
    }

}
