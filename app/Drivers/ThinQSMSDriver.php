<?php

namespace App\Drivers;

use App\Jobs\LogEvent;
use App\Jobs\SaveMessage;
use App\Jobs\SendThinqSMS;
use App\Models\Carrier;
use App\Models\Message;
use App\Models\Number;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use NumberFormatter;

class ThinQSMSDriver implements SMSDriver
{
    private int $maxMessageLength = 910;

    private string $requestInputMessageKey = 'message';

    private string $requestInputUidKey = 'guid';

    private string $requestInputStatusKey = 'send_status';

    private array $carrierValidationFields = [
        'thinq_account_id' => 'required',
        'thinq_api_username' => 'required',
        'thinq_api_token' => 'required',
    ];

    public function getType(string $identifier): string
    {
        return 'PN';
    }

    public function getFriendlyType(string $identifier): string
    {
        return 'Phone Number';
    }

    public function queueOutbound($host, $carrier, $recipient, $message, $messageID, $reply_with): void
    {
        SendThinqSMS::dispatch($host, $carrier, $recipient, $message, $messageID, $reply_with);
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
        //https://apidocs.thinq.com/?version=latest#7c5909e8-596c-47b3-9f24-438196eef374
        //all requests come from 192.81.236.250
        if ($request->getClientIp() === '192.81.236.250') {
            return true;
        }

        LogEvent::dispatch(
            'Failed inbound message',
            get_class($this), 'error', json_encode('Request not from ThinQ documented IP address'), null
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
            encrypt($request->input($this->getRequestInputMessageKey())),
            null,
            $submitted_at,
            $reply_with,
            $request->header('X-sms-guid'),
            'inbound'
        );
    }

    public function updateMessageStatus(Request|null $request, Carrier $carrier, Message $message): bool
    {
        /*
        * https://api.thinq.com/account/{{account_id}}/product/origination/sms/{{message_id}}
        */
        try {
            $thinq = new Guzzle([
                'timeout' => 10.0,
                'base_uri' => 'https://api.thinq.com',
                'auth' => [$carrier->thinq_api_username, decrypt($carrier->thinq_api_token)],
            ]);
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failed decrypting carrier api token',
                get_class($this), 'error', json_encode($carrier->toArray()), null
            );

            return false;
        }

        try {
            $result = $thinq->get("account/{$carrier->thinq_account_id}/product/origination/sms/{$message->carrier_message_uid}");
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failure synchronizing message status',
                get_class($this), 'error', json_encode($e->getMessage()), null
            );

            return false;
        }

        if ($result->getStatusCode() !== 200) {
            LogEvent::dispatch(
                'Failure synchronizing message status',
                get_class($this), 'error', json_encode($result->getReasonPhrase()), null
            );

            return false;
        }

        $body = $result->getBody();
        $json = $body->getContents();
        $arr = json_decode($json, true);
        if (! isset($arr['delivery_notifications'])) {
            LogEvent::dispatch(
                'Failure getting delivery notifications',
                get_class($this), 'error', json_encode([$arr, $arr['delivery_notifications']]), null
            );

            return false;
        }

        $ts = null;
        $latest_update = null;

        foreach ($arr['delivery_notifications'] as $dn) {
            if ($ts === null || Carbon::parse($dn['timestamp']) >= $ts) {
                $ts = Carbon::parse($dn['timestamp']);
                $latest_update = $dn;
            }
        }

        if ($latest_update !== null) {
            if ($message->status !== $latest_update['send_status']) {
                // Only send notifications when there is a new update
                LogEvent::dispatch(
                    'Delivery Notifications Update',
                    get_class($this), 'info', json_encode([$message->toArray(), $arr['delivery_notifications']]), null
                );
            }

            switch (strtolower($latest_update['send_status'])) {
                case 'sent':
                case 'delivrd':
                case 'delivered':
                    $message->status = $latest_update['send_status'];
                    $message->delivered_at = Carbon::parse($latest_update['timestamp']);
                    break;
                case 'rejectd':
                case 'expired':
                case 'deleted':
                case 'unknown':
                case 'failed':
                case 'undelivered':
                case 'undeliv':
                    $message->status = $latest_update['send_status'];
                    $message->failed_at = Carbon::parse($latest_update['timestamp']);
                    break;
                default:
                    break;
            }
        }

        try {
            $message->save();
        } catch (Exception $e) {
            LogEvent::dispatch(
                'Failure updating message status',
                get_class($this), 'error', json_encode($e->getMessage()), null
            );

            return false;
        }

        return true;
    }

    public function provisionNumber(Carrier $carrier, string $identifier): bool
    {
        $ipify = new Guzzle(['base_uri' => 'https://api.ipify.org']);

        try {
            $response = $ipify->get('/');
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        $ip = (string) $response->getBody();
        $validator = Validator::make(['ip' => $ip], ['ip' => 'required|ip']);

        if ($validator->fails()) {
            Log::debug('IP validation failed');

            return false;
        }

        try {
            $thinq = new Guzzle([
                'base_uri' => 'https://api.thinq.com',
                'auth' => [$carrier->thinq_api_username, decrypt($carrier->thinq_api_token)],
                'headers' => ['content-type' => 'application/json'],
            ]);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        //get all current ip whitelists
        $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/ip";

        try {
            $res = $thinq->get($url);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        $hasIP = false;
        $list = json_decode((string) $res->getBody(), true);
        foreach ($list['rows'] as $row) {
            if ($row['ip'] == $ip) {
                $hasIP = true;
                break;
            }
        }

        //if our public ip is in the whitelist list, continue
        //if our public ip is not in the whitlelist list, add it
        if (! $hasIP) {
            $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/ip/{$ip}";
            try {
                $res = $thinq->post($url);
            } catch (Exception $e) {
                Log::debug($e->getMessage());

                return false;
            }
        }

        //get all current sms routing profiles
        $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/profile";
        try {
            $res = $thinq->get($url);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        $hasProfile = false;
        $list = json_decode((string) $res->getBody(), true);
        $sms_routing_profile = '';

        foreach ($list['rows'] as $row) {
            if ($row['name'] == $identifier) {
                $sms_routing_profile = $row['id'];
                $hasProfile = true;
                break;
            }
        }

        //if our url is in the profile list, continue
        //if our url is not in the profile list, add it
        if (! $hasProfile) {
            $webhook = secure_url("/sms/inbound/{$identifier}/primary");
            $body = [
                'sms_routing_profile' => [
                    'name' => $identifier,
                    'url' => $webhook,
                    'attachment_type' => 'url',
                ],
            ];

            $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/profile";
            try {
                $res = $thinq->post($url, ['body' => json_encode($body)]);
            } catch (Exception $e) {
                Log::debug($e->getMessage());

                return false;
            }

            $profile = json_decode((string) $res->getBody(), true);

            $sms_routing_profile = $profile['id'];
        } else {
            //update it so we enesure it has our most recent url
            $webhook = secure_url("/sms/inbound/{$identifier}/primary");
            $body = [
                'sms_routing_profile' => [
                    'name' => $identifier,
                    'url' => $webhook,
                    'attachment_type' => 'url',
                ],
            ];

            $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/profile/{$sms_routing_profile}";
            try {
                $res = $thinq->put($url, ['body' => json_encode($body)]);
            } catch (Exception $e) {
                Log::debug($e->getMessage());

                return false;
            }
        }

        //set our outbound message status url
        //update it so we enesure it has our most recent url
        $webhook = secure_url("/sms/callback/{$identifier}/status");
        $body = [
            'settings' => [
                'deliveryConfirmationUrl' => $webhook,
                'deliveryNotificationType' => 'form-data',
            ],
        ];

        $url = "/account/{$carrier->thinq_account_id}/product/origination/sms/settings/outbound";
        try {
            $res = $thinq->post($url, ['body' => json_encode($body)]);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        //create a feature order to do the following:
        //  enable SMS
        //  associate sms routing profile
        $body = [
            'order' => [
                'tns' => [
                    [
                        'sms_routing_profile_id' => $sms_routing_profile,
                        'features' => ['cnam' => false, 'e911' => false, 'sms' => true],
                        'did' => $identifier,
                    ],
                ],
            ],
        ];

        $url = "/account/{$carrier->thinq_account_id}/origination/did/features/create";
        try {
            $res = $thinq->post($url, ['body' => json_encode($body)]);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        $order = json_decode((string) $res->getBody(), true);

        // complete feature order
        $url = "/account/{$carrier->thinq_account_id}/origination/did/features/complete/{$order['order']['id']}";
        try {
            $res = $thinq->post($url);
        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return false;
        }

        return true;
    }

    public function getCarrierDetails(Carrier $carrier, string $identifier): array
    {
        $number = Number::where('identifier', $identifier)->first();

        return Arr::dot(array_merge(['carrier' => $carrier->only([
            'id', 'name', 'priority', 'api', 'enabled', 'beta', 'created_at', 'updated_at',
        ]), 'number' => $number->toArray() ?? []]));
    }

    public function getAvailableNumbers(Request $request, Carrier $carrier): array
    {
        $available = [];
        $page = $request->get('page') ?? 1;

        $url = "/origination/did/search2/did/{$carrier->thinq_account_id}?page={$page}&rows=100";

        $guzzle = new Guzzle(
            ['base_uri' => 'https://api.thinq.com']
        );
        try {
            $res = $guzzle->get($url, ['auth' => [$carrier->thinq_api_username, decrypt($carrier->thinq_api_token)]]);
        } catch (RequestException $e) {
        } catch (Exception $e) {
        }

        $thinq_numbers = json_decode((string) $res->getBody(), true);

        if ($thinq_numbers['total_rows'] > 0) {
            foreach ($thinq_numbers['rows'] as $thinq_number) {
                $available[] = [
                    'id' => $thinq_number['id'],
                    'api' => $carrier->api,
                    'type' => 'Phone Number',
                    'number' => "+{$thinq_number['id']}",
                    'carrier' => $carrier,
                    'details' => Arr::dot($thinq_number),
                    'sms_enabled' => $thinq_number['provisioned'],
                ];
            }
        }

        $pages = 0;

        if ($thinq_numbers['has_next_page'] === true) {
            $pages = (int) floor($thinq_numbers['total_rows'] / $thinq_numbers['rows_per_page']);
        }

        return [
            'available' => $available,
            'pages' => $pages,
        ];
    }

    public function verifyCarrierValidation(Request $request): RedirectResponse | View
    {
        $validator = Validator::make($request->toArray(), $this->carrierValidationFields);
        if ($validator->fails()) {
            return redirect()->to('/carriers')->withErrors($validator->errors());
        }

        $url = "/account/{$request->input('thinq_account_id')}/balance";
        $guzzle = new Guzzle(
            ['base_uri' => 'https://api.thinq.com']
        );
        try {
            $res = $guzzle->request('GET', $url, ['auth' => [$request->input('thinq_api_username'), $request->input('thinq_api_token')]]);
        } catch (RequestException $e) {
            return redirect()->to('/carriers')->withErrors(["Unable to connect to ThinQ account: {$e->getMessage()}"]);
        } catch (Exception $e) {
            return redirect()->to('/carriers')->withErrors(["Unable to connect to ThinQ account: {$e->getMessage()}"]);
        }

        try {
            $balance = json_decode((string) $res->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return redirect()->to('/carriers')->withErrors(["Unable to connect to ThinQ account: {$e->getMessage()}"]);
        }

        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $balance['balance'] = $fmt->formatCurrency($balance['balance'], 'USD');

        $account = [
            'balance' => $balance['balance'],
            'account_id' => $request->input('thinq_account_id'),
            'api_username' => $request->input('thinq_api_username'),
            'api_token' => $request->input('thinq_api_token'),
        ];

        return view('carriers.thinq-verify')->with('account', $account);
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
        $carrier->beta = 0;
        $carrier->priority = $request->input('priority');
        $carrier->thinq_account_id = $request->input('thinq_account_id');
        $carrier->thinq_api_username = $request->input('thinq_api_username');
        $carrier->thinq_api_token = encrypt($request->input('thinq_api_token'));
        $carrier->api = 'thinq';

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
                'thinq_account_id' => $carrier->thinq_account_id,
                'thinq_api_username' => $carrier->thinq_api_username,
                'thinq_api_token' => decrypt($carrier->thinq_api_token),
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    public function showCarrierImageDetails(): array
    {
        return [
            'url' => '/images/thinq-badge.svg',
            'title' => 'Powered by ThinQ/Commio',
        ];
    }

    public function showCarrierDetails(Carrier $carrier): string
    {
        return  $carrier->thinq_account_id ?? 'unknown'.' / '.$carrier->thinq_api_username ?? 'unknown';
    }

    public function canAutoProvision(): bool
    {
        return true;
    }
}
