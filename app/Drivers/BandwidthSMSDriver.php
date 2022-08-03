<?php

namespace App\Drivers;

use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendBandwidthSMS;
use App\Models\Carrier;
use App\Models\Message;
use App\Models\Number;
use BandwidthLib\APIException;
use BandwidthLib\BandwidthClient;
use BandwidthLib\Configuration;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BandwidthSMSDriver implements SMSDriver
{
    private int $maxMessageLength = 2048;

    private string $requestInputMessageKey = 'message.text';

    private string $requestInputUidKey = 'message.id';

    private string $requestInputStatusKey = 'type';

    private string $requestInputToKey = 'to';

    private string $requestInputFromKey = 'message.from';

    private array $carrierValidationFields = [
        'bandwidth_api_username' => 'required',
        'bandwidth_api_password' => 'required',
        'bandwidth_api_account_id' => 'required',
        'bandwidth_api_application_id' => 'required',
    ];

    public function getType(string $identifier): string
    {
        return 'PN' ?? $identifier;
    }

    public function getFriendlyType(string $identifier): string
    {
        return 'Phone Number' ?? $identifier;
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
        SendBandwidthSMS::dispatch($host, $carrier, $recipient, $message, $messageID, $reply_with);
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
        return response(json_encode(['success' => true]), 200, ['content-type' => 'application/json']);
    }

    public function verifyHandlerRequest(Request $request, Carrier $carrier): bool
    {
        // Ensure Bandwidth's UserAgent and application_id are set/match expected values
        // Helps mitigate against non-targeted attacks (meaning they have to know the application_id to make a dent)

        $validator = Validator::make([
            'user_agent' => $request->userAgent(),
            'application_id' => $request->input('message.applicationId'),
        ], [
            'user_agent' => 'required|string|in:BandwidthAPI/v2',
            'application_id' => "required|string|in:{$carrier->bandwidth_api_application_id}",
        ]);

        if ($validator->fails()) {
            return false;
        }

        return true;
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
            encrypt($request->input($this->getRequestInputMessageKey())),
            null,
            $submitted_at,
            $reply_with,
            $request->input($this->getRequestInputUidKey()),
            'inbound'
        );
    }

    public function updateMessageStatus(Request|null $request, Carrier $carrier, Message $message): bool
    {
        if ($request === null) {
            //it's not a webhook
        }
    }

    public function provisionNumber(Carrier $carrier, $identifier): bool
    {
        return true;
    }

    public function getCarrierDetails(Carrier $carrier, string $identifier): array
    {
        $number = Number::where('identifier', $identifier)->first();

        return Arr::dot(array_merge(['carrier' => $carrier->only([
            'id', 'name', 'bandwidth_api_username', 'bandwidth_api_account_id', 'bandwidth_api_application_id', 'priority', 'api', 'enabled', 'beta', 'created_at', 'updated_at',
        ]), 'number' => $number->toArray() ?? []]));
    }

    public function getAvailableNumbers(Request $request, Carrier $carrier): array
    {
        return ['available' => [], 'pages' => null];
    }

    public function verifyCarrierValidation(Request $request): RedirectResponse | View
    {
        $validator = Validator::make($request->toArray(), $this->carrierValidationFields);
        if ($validator->fails()) {
            return redirect()->to('/carriers')->withErrors($validator->errors());
        }

        try {
            $config = new Configuration([
                'messagingBasicAuthUserName' => $request->input('bandwidth_api_username'),
                'messagingBasicAuthPassword' => $request->input('bandwidth_api_password'),
            ]);

            $client = new BandwidthClient($config);

            $messagingClient = $client->getMessaging()->getClient();

            try {
                $response = $messagingClient->getMessages($request->input('bandwidth_api_account_id'),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1);
                $bandwidth_response = $response->getResult();
            } catch (APIException $e) {
                return redirect()->to('/carriers')->withErrors(['Unable to connect to your Bandwidth account']);
            }
        } catch (Exception $e) {
            return redirect()->to('/carriers')->withErrors(['Unable to connect to your Bandwidth account']);
        }

        return view('carriers.bandwidth-verify')->with('account', $request->toArray());
    }

    public function createCarrierInstance(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->toArray(), $this->carrierValidationFields);
        if ($validator->fails()) {
            return redirect()->to('/carriers')->withErrors($validator->errors());
        }

        $carrier = new Carrier;
        $carrier->name = $request->input('name');
        $carrier->enabled = 0;
        $carrier->beta = 1;
        $carrier->priority = $request->input('priority');
        $carrier->bandwidth_api_username = $request->input('bandwidth_api_username');
        $carrier->bandwidth_api_password = encrypt($request->input('bandwidth_api_password'));
        $carrier->bandwidth_api_account_id = $request->input('bandwidth_api_account_id');
        $carrier->bandwidth_api_application_id = $request->input('bandwidth_api_application_id');
        $carrier->api = 'bandwidth';

        try {
            $carrier->save();
        } catch (Exception $e) {
            return redirect()->to('/carriers')->withErrors([__('Unable to save carrier')]);
        }

        LogEvent::dispatch(
            "{$carrier->name} ({$carrier->api}) created",
            get_class($this), 'info', json_encode($carrier->toArray()), $request->user() ?? null
        );

        $statusHtml = 'Carrier successfully created!';

        return redirect()->to('/carriers')
            ->with('status', $statusHtml);
    }

    public function showCarrierCredentials(Carrier $carrier): array
    {
        try {
            return [
                'bandwidth_api_username' => $carrier->bandwidth_api_username,
                'bandwidth_api_password' => decrypt($carrier->bandwidth_api_password),
                'bandwidth_api_account_id' => $carrier->bandwidth_api_account_id,
                'bandwidth_api_application_id' => $carrier->bandwidth_api_application_id,
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    public function showCarrierImageDetails(): array
    {
        return [
            'url' => '/images/bandwidth-badge.svg',
            'title' => 'Powered by Bandwidth.com',
        ];
    }

    public function showCarrierDetails(Carrier $carrier): string
    {
        return  $carrier->bandwidth_api_account_id ?? 'unknown';
    }

    public function canAutoProvision(): bool
    {
        return true;
    }
}
