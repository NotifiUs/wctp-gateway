<?php

namespace App\Drivers;

use App\Carrier;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendThinqSMS;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
}
