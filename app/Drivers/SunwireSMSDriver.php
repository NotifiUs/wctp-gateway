<?php

namespace App\Drivers;

use App\Models\Number;
use Exception;
use Carbon\Carbon;
use App\Jobs\LogEvent;
use App\Models\Carrier;
use App\Models\Message;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Jobs\SaveMessage;
use App\Jobs\SendSunwireSMS;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class SunwireSMSDriver implements SMSDriver
{
    private array $json;
    private int $maxMessageLength = 8192;
    private string $requestInputMessageKey = 'Body';
    private string $requestInputUidKey = 'ID';
    private string $requestInputStatusKey = 'Status';

    public function getType( string $identifier ): string
    {
        return 'PN';
    }

    public function getFriendlyType( string $identifier ): string
    {
        return 'Phone Number';
    }

    public function queueOutbound($host, $carrier, $recipient, $message, $messageID, $reply_with): void
    {
        SendSunwireSMS::dispatch( $host, $carrier, $recipient, $message, $messageID, $reply_with );
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
        return response( json_encode([ 'Status' => "OK" ]), 200, ['content-type' => 'application/json']);
    }

    public function verifyHandlerRequest( Request $request, Carrier $carrier ): bool
    {
        //Sunwire SMS Messaging API v1.7.pdf
        //MO/Reports requests will come from one of the following IP addresses:
        $allowed_ips = [
            '216.180.103.4',
            '209.91.135.233'
        ];

        if( in_array($request->getClientIp(), $allowed_ips))
        {
            return true;
        }

        LogEvent::dispatch(
            "Failed inbound message",
            get_class( $this ), 'error', json_encode(['error' => "Request not from SunWire documented IP address", 'unknown_ip' => $request->getClientIp()]), null
        );

        return false;
    }

    public function getRequestInputMessageKey(): string
    {
        return $this->requestInputMessageKey;
    }

    public function saveInboundMessage(Request $request, int $carrier_id, int $number_id, int $enterprise_host_id, Carbon $submitted_at, $reply_with = null): void
    {
        $this->json = json_decode($request->getContent(), true );

        if($this->json['Type'] === 'Message')
        {
            SaveMessage::dispatch(
                $carrier_id,
                $number_id,
                $enterprise_host_id,
                $this->json['To'],
                $this->json['From'],
                encrypt( $this->json[$this->getRequestInputMessageKey()] ),
                null,
                $submitted_at,
                $reply_with,
                $this->json[$this->getRequestInputUidKey()],
                'inbound'
            );
        }
        elseif( $this->json['Type'] === 'Report')
        {
            $message = Message::where('identifier', $this->json['ID'])->first();
            $carrier = Carrier::find($carrier_id);
            if($this->updateMessageStatus( $request, $carrier, $message ) === false )
            {
                LogEvent::dispatch(
                    "Unable to update Sunwire message status",
                    get_class( $this ), 'error', json_encode($this->json), null
                );
            }
        }
        else
        {
            LogEvent::dispatch(
                "Unknown Sunwire webhook Type",
                get_class( $this ), 'error', json_encode($this->json), null
            );
        }

    }

    public function updateMessageStatus(Request|null $request, Carrier $carrier, Message $message ): bool
    {
        if($request === null)
        {
            return true;
        }

        /**
         *
            Type ‘Report’ – Indicates the type of request
            ID The original unique identifier of the message
            From The original sender of the message
            To The original recipient of the message
            Status Indicates the final status of the message. The value is numeric and may be one of:
            0 - DELIVERED - The message has been delivered.
            1 - EXPIRED - The message could not be delivered.
            2 - DELETED - The message has been deleted.
            3 – IN-TRANSIT – The message is in-transit (intermediate status).
            5 - UNDELIVERABLE / FAILED - The message could not be delivered.
            7 - UNKNOWN - The status of the message is unknown.
            Reason Indicates the reason the message has reached this status.
         */

        $statusCodes = [
            0 => 'DELIVERED',
            1 => 'EXPIRED',
            2 => 'DELETED',
            3 => 'IN-TRANSIT',
            4 => 'UNKNOWN',
            5 => 'FAILED',
            6 => 'UNKNOWN',
            7 => 'UNKNOWN',
        ];

        $this->json = json_decode($request->getContent(), true );

        $status = $this->json['Status'] ?? 7;

        Log::info(print_r($this->json, true));

        switch( $status ) {
            case 0: //delivered
                $message->status = $statusCodes[$status] ?? "UNKNOWN";
                $message->delivered_at = Carbon::now();
                break;
            case 3: //in-transit
                $message->status = $statusCodes[$status]?? "UNKNOWN";
                $message->delivered_at = Carbon::now();
                break;
            case 1: //expired
            case 2: //deleted
            case 5: //undeliverable / failed
            case 7: //unknown
                $message->status = $statusCodes[$status] ?? "UNKNOWN";
                $message->failed_at = Carbon::now();
                break;
            default:
                LogEvent::dispatch(
                    "Unknown Sunwire Status response",
                    get_class( $this ), 'error', json_encode($this->json), null
                );
                return false;
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

    public function provisionNumber(Carrier $carrier, $identifier): bool
    {
        return true;
    }

    public function getCarrierDetails(Carrier $carrier, string $identifier ): array
    {
        $number = Number::where('identifier', $identifier)->first();
        return Arr::dot(array_merge(['carrier' => $carrier->only([
            'id','name','priority', 'api', 'enabled', 'beta', 'created_at', 'updated_at'
        ]), 'number' => $number->toArray() ?? [] ]));

    }

    public function getAvailableNumbers(Request $request, Carrier $carrier ): array
    {
        return ['available' => [], 'pages' => null ];
    }

    function verifyCarrierValidation( Request $request ): RedirectResponse | View
    {
        return view('carriers.sunwire-verify')->with('account', $request->toArray() );
    }

    public function createCarrierInstance(Request $request): RedirectResponse
    {
        $carrier = new Carrier;
        $carrier->name = $request->input('name');
        $carrier->enabled = 0;
        $carrier->beta = 1;
        $carrier->priority = $request->input('priority');
        $carrier->api = 'sunwire';

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->to('/carriers')->withErrors([__('Unable to save carrier'), $e->getMessage()]); }

        LogEvent::dispatch(
            "{$carrier->name} ({$carrier->api}) created",
            get_class( $this ), 'info', json_encode($carrier->toArray()), $request->user() ?? null
        );

        $statusHtml = "Carrier successfully created!";
        return redirect()->to('/carriers')
            ->with('status', $statusHtml);
    }

    public function showCarrierCredentials( Carrier $carrier ): array
    {
       return [
           'Whitelisting' => 'Please contact Sunwire directly.'
       ];
    }

    public function showCarrierImageDetails(): array
    {
        return [
            'url' => '/images/sunwire-badge.svg',
            'title' => 'Powered by Sunwire',
        ];
    }

    public function showCarrierDetails(Carrier $carrier ): string
    {
        return  'https://sunwire.ca';
    }

    public function canAutoProvision(): bool
    {
        return false;
    }
}
