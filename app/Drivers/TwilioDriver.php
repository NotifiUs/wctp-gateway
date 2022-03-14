<?php

namespace App\Drivers;

use Exception;
use App\Carrier;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendTwilioSMS;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Twilio\Security\RequestValidator;

class TwilioDriver implements Driver
{
    private int $maxMessageLength = 1600;
    private string $requestInputMessageKey = 'Body';
    private string $requestInputUidKey = 'MessageSid';
    private string $requestInputStatusKey = 'MessageStatus';

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

}
