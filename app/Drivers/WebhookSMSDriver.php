<?php

namespace App\Drivers;

use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendWebhookSMS;
use App\Models\Carrier;
use App\Models\Message;
use App\Models\Number;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WebhookSMSDriver implements SMSDriver
{
    private array $json;

    private int $maxMessageLength = 8192;

    private string $requestInputMessageKey = 'message';

    private string $requestInputUidKey = 'id';

    private string $requestInputStatusKey = 'status';

    private string $requestInputToKey = 'to';

    private string $requestInputFromKey = 'from';

    private array $carrierValidationFields = [
        'webhook_host' => 'required|url|starts_with:https',
        'webhook_endpoint' => 'required',
        'webhook_username' => 'required',
        'webhook_password' => 'required',
    ];

    public function getType(string $identifier): string
    {
        return 'WH';
    }

    public function getFriendlyType(string $identifier): string
    {
        return 'Web Hook';
    }

    public function queueOutbound($host, $carrier, $recipient, $message, $messageID, $reply_with): void
    {
        SendWebhookSMS::dispatch($host, $carrier, $recipient, $message, $messageID, $reply_with);
    }

    public function getRequestInputToKey(): string
    {
        return $this->requestInputToKey;
    }

    public function getRequestInputFromKey(): string
    {
        return $this->requestInputFromKey;
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
        if ($request->wantsJson()) {
            $this->json = json_decode($request->getContent(), true);
            $to = $this->json[$this->getRequestInputToKey()];
            $from = $this->json[$this->getRequestInputFromKey()];
            $message = $this->json[$this->getRequestInputMessageKey()];
            $id = $this->json[$this->getRequestInputUidKey()];
        } else {
            $to = $request->input($this->getRequestInputToKey());
            $from = $request->input($this->getRequestInputFromKey());
            $message = $request->input($this->getRequestInputMessageKey());
            $id = $request->input($this->getRequestInputUidKey());
        }

        $validator = Validator::make([
            'to' => $to,
            'from' => $from,
            'message' => $message,
            'id' => $id,
        ], [
            'to' => 'required',
            'from' => 'required',
            'message' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        //We are using the same user/pass for inbound/outbound webhooks ...I can see this needing changed at some point
        //but for now it forces people to use TLS with a valid certificate + Basic Auth which is demonstratively common/safe in most cases
        if ($request->getUser() === decrypt($carrier->webhook_username) && $request->getPassword() === decrypt($carrier->webhook_password)) {
            return true;
        }

        return false;
    }

    public function getRequestInputMessageKey(): string
    {
        return $this->requestInputMessageKey;
    }

    public function saveInboundMessage(Request $request, int $carrier_id, int $number_id, int $enterprise_host_id, Carbon $submitted_at, $reply_with = null): void
    {
        if ($request->wantsJson()) {
            $this->json = json_decode($request->getContent(), true);
            $to = $this->json[$this->getRequestInputToKey()];
            $from = $this->json[$this->getRequestInputFromKey()];
            $message = $this->json[$this->getRequestInputMessageKey()];
            $uid = $this->json[$this->getRequestInputUidKey()];
        } else {
            $to = $request->input($this->getRequestInputToKey());
            $from = $request->input($this->getRequestInputFromKey());
            $message = $request->input($this->getRequestInputMessageKey());
            $uid = $request->input($this->getRequestInputUidKey());
        }
        try {
            SaveMessage::dispatch(
                $carrier_id,
                $number_id,
                $enterprise_host_id,
                $to,
                $from,
                encrypt($message),
                null,
                $submitted_at,
                $reply_with,
                $uid,
                'inbound'
            );
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Unable to save inbound message',
                get_class($this), 'info', json_encode(['error' => $e->getMessage(), 'to' => $to, 'from' => $from, 'message' => encrypt($message), 'status' => $uid]), $request->user() ?? null
            );
        }
    }

    public function updateMessageStatus(Request|null $request, Carrier $carrier, Message $message): bool
    {
        //mark message as delivered, as it was already sent
        $message->status = 'delivered';
        $message->delivered_at = Carbon::now();
        try {
            $message->save();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function provisionNumber(Carrier $carrier, $identifier): bool
    {
        //there is nothing to provision for webhooks
        //maybe add in auth credentials for inbound at some point?
        return true;
    }

    public function getCarrierDetails(Carrier $carrier, string $identifier): array
    {
        $number = Number::where('identifier', $identifier)->first();

        return Arr::dot(array_merge(['carrier' => $carrier->only([
            'id', 'name', 'webhook_host', 'webhook_endpoint', 'webhook_username', 'webhook_password', 'priority', 'api', 'enabled', 'beta', 'created_at', 'updated_at',
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
            return redirect()->back()->withErrors($validator->errors());
        }

        return view('carriers.webhook-verify')->with('account', $request->toArray());
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
        $carrier->webhook_host = $request->input('webhook_host');
        $carrier->webhook_endpoint = $request->input('webhook_endpoint');
        $carrier->webhook_username = encrypt($request->input('webhook_username'));
        $carrier->webhook_password = encrypt($request->input('webhook_password'));
        $carrier->api = 'webhook';

        try {
            $carrier->save();
        } catch (Exception $e) {
            return redirect()->to('/carriers')->withErrors([__('Unable to save carrier'), $e->getMessage()]);
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
                'webhook_host' => $carrier->webhook_host,
                'webhook_endpoint' => $carrier->webhook_endpoint,
                'webhook_username' => decrypt($carrier->webhook_username),
                'webhook_password' => decrypt($carrier->webhook_password),
                'inboud_endpoint' => secure_url('/sms/inbound/{identifier}/primary'),
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    public function showCarrierImageDetails(): array
    {
        return [
            'url' => '/images/webhook-badge.svg',
            'title' => 'Powered by HTTP',
        ];
    }

    public function showCarrierDetails(Carrier $carrier): string
    {
        return  $carrier->webhook_host.$carrier->webhook_endpoint;
    }

    public function canAutoProvision(): bool
    {
        return false;
    }
}
