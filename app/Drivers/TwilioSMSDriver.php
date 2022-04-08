<?php

namespace App\Drivers;

use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendTwilioSMS;
use App\Models\Carrier;
use App\Models\Message;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Security\RequestValidator;

class TwilioSMSDriver implements SMSDriver
{
    private int $maxMessageLength = 1600;
    private string $requestInputMessageKey = 'Body';
    private string $requestInputUidKey = 'MessageSid';
    private string $requestInputStatusKey = 'MessageStatus';
    private string $requestInputToKey = 'To';
    private string $requestInputFromKey = 'From';
    private array $carrierValidationFields = [
        'twilio_account_sid' => 'required',
        'twilio_auth_token' => 'required',
    ];

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

    public function getRequestInputToKey(): string
    {
        return $this->requestInputToKey;
    }

    public function getRequestInputFromKey(): string
    {
        return $this->requestInputFromKey;
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
            $request->header('X-Twilio-Signature') ?? $_SERVER["HTTP_X_TWILIO_SIGNATURE"],
            $request->fullUrl(),
            $request->post()
        ))
        {
            return true;
        }

        LogEvent::dispatch(
            "Failed inbound message",
            get_class( $this ), 'error', json_encode(["Unable to verify Twilio request", 'header' => $request->headers(), 'post' => $request->post()]), null
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
            $request->input($this->getRequestInputToKey()),
            $request->input($this->getRequestInputFromKey()),
            encrypt( $request->input($this->getRequestInputMessageKey()) ),
            null,
            $submitted_at,
            $reply_with,
            $request->input($this->getRequestInputUidKey()),
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

                return Arr::dot(array_merge(['carrier' => $carrier->only([
                    'id','name', 'priority', 'api', 'enabled', 'beta', 'created_at', 'updated_at'
                ]), 'number' => $results->toArray(), 'addons' => $serviceAddons ]));
            }
            else {
                $results = $twilio->incomingPhoneNumbers($identifier)->fetch();
                return Arr::dot(array_merge(['carrier' => $carrier->only([
                    'id','name', 'priority', 'api', 'enabled', 'beta', 'created_at', 'updated_at'
                ]), 'number' => $results ]));
            }

        }catch( Exception $e ) { return []; }
    }

    public function getAvailableNumbers(Request $request, Carrier $carrier ): array
    {
        $available = [];

        //get list of numbers from messaging services
        try {
            $twilio = new TwilioClient( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );
            $services = $twilio->messaging->v1->services->read(100, 100);

            foreach ( $services as $record ) {
                //check to see if it has phonenumbers or shortcodes
                $hasNumbers = false;
                $hasShortCodes = false;
                $serviceAddons = [];
                $service_name = $record->friendlyName;
                foreach( $record->phoneNumbers->read(100, 100) as $num )
                {
                    $hasNumbers = true;
                    $service_name = $num->phoneNumber;
                    $exclude[] = $num->sid;
                    $serviceAddons['numbers'][] = $num->toArray();
                }

                foreach( $record->shortCodes->read(100, 100) as $shortcode )
                {
                    $hasShortCodes = true;
                    $service_name = $shortcode->shortCode;
                    $exclude[] = $shortcode->sid;
                    $serviceAddons['shortcodes'][] = $shortcode->toArray();
                }

                $available[] = [
                    'id' => $record->sid,
                    'api' => $carrier->api,
                    'type' => 'Messaging Service',
                    'number' => $service_name,
                    'carrier' => $carrier,
                    'details' => Arr::dot( array_merge($record->toArray(), $serviceAddons ) ),
                    'sms_enabled' => $hasNumbers || $hasShortCodes,
                ];
            }
        }
        catch( Exception $e ) {}

        //get list of numbers
        try {
            $twilio = new TwilioClient( $carrier->twilio_account_sid, decrypt( $carrier->twilio_auth_token ) );
            $incomingPhoneNumbers = $twilio->incomingPhoneNumbers->read(array(
                ['capabilities' => [
                    'sms' => 1
                ]]
            ), 100);

            foreach ( $incomingPhoneNumbers as $record ) {

                if( in_array( $record->sid, $exclude ) )
                {
                    $available[] = [
                        'id' => $record->sid,
                        'api' => $carrier->api,
                        'type' => 'Phone Number',
                        'number' => $record->phoneNumber,
                        'carrier' => $carrier,
                        'details' => Arr::dot($record->toArray()),
                        'sms_enabled' => 0,
                    ];
                }
                else
                {
                    $available[] = [
                        'id' => $record->sid,
                        'api' => $carrier->api,
                        'type' => 'Phone Number',
                        'number' => $record->phoneNumber,
                        'carrier' => $carrier,
                        'details' => Arr::dot($record->toArray()),
                        'sms_enabled' => $record->capabilities['sms'],
                    ];
                }

            }
        }
        catch( Exception $e ){}
        return [
            'available' => $available,
            'pages' => null
        ];
    }

    public function verifyCarrierValidation( Request $request ): RedirectResponse | View
    {
        $validator = Validator::make($request->toArray(), $this->carrierValidationFields);
        if($validator->fails())
        {
            return redirect()->to('/carriers')->withErrors($validator->errors());
        }

        try {
            $twilio = new TwilioClient( $request->input('twilio_account_sid'), $request->input('twilio_auth_token'));
            $account = $twilio->api->v2010->accounts($request->input('twilio_account_sid'))->fetch();
        }
        catch( Exception $e )
        {
            return redirect()->to('/carriers')->withErrors(['Unable to connect to Twilio account']);
        }

        return view('carriers.twilio-verify')->with('account', $account->toArray() );

    }

    public function createCarrierInstance(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->toArray(), $this->carrierValidationFields);
        if($validator->fails())
        {
            return redirect()->to('/carriers')->withErrors($validator->errors());
        }

        $carrier = new Carrier;
        $carrier->name = $request->input('name');
        $carrier->enabled = 0;
        $carrier->beta = 0;
        $carrier->priority = $request->input('priority');
        $carrier->twilio_account_sid = $request->input('twilio_account_sid');
        $carrier->twilio_auth_token = encrypt( $request->input('twilio_auth_token') );
        $carrier->api = 'twilio';

        try{ $carrier->save(); }catch( Exception $e ){ return redirect()->to('/carriers')->withErrors([__('Unable to save carrier')]); }

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
        try {
            return [
                'twilio_account_sid' => $carrier->twilio_account_sid,
                'twilio_auth_token' => decrypt($carrier->twilio_auth_token),
            ];
        }
        catch(Exception $e )
        {
            return [];
        }
    }

    public function showCarrierImageDetails(): array
    {
        return [
            'url' => '/images/twilio-badge.png',
            'title' => 'Powered by Twilio',
        ];
    }

    public function showCarrierDetails(Carrier $carrier ): string
    {
        return  $carrier->twilio_account_sid ?? 'unknown';
    }

    public function canAutoProvision(): bool
    {
        return true;
    }
}
