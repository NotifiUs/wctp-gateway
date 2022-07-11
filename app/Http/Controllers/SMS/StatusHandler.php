<?php

namespace App\Http\Controllers\SMS;

use App\Drivers\DriverFactory;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Message;
use App\Models\Number;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatusHandler extends Controller
{
    private $driver;

    private Carrier|null $carrier = null;

    public function __invoke(Request $request, string $identifier): Response
    {
        $number = Number::where('enabled', 1)->where('identifier', $identifier)->first();

        if ($number === null) {
            return $this->respond();
        }

        $this->carrier = Carrier::where('enabled', 1)->where('id', $number->carrier_id)->first();

        if ($this->carrier === null) {
            return $this->respond();
        }

        try {
            $driverFactory = new DriverFactory($this->carrier->api);
            $this->driver = $driverFactory->loadDriver();
        } catch (Exception $e) {
            return $this->respond();
        }

        if (! $this->verify($request)) {
            return $this->respond();
        }

        $carrier_uid = $request->input($this->driver->getRequestInputUidKey());

        $message = Message::where('carrier_message_uid', $carrier_uid)->first();

        if ($message === null) {
            return $this->respond();
        }

        $message->status = $request->input($this->driver->getRequestInputStatusKey()) ?? '';

        switch (strtolower($message->status)) {
            case 'sent':
            case 'delivrd':
            case 'delivered':
                $message->delivered_at = Carbon::now();
                break;
            case 'rejectd':
            case 'expired':
            case 'deleted':
            case 'unknown':
            case 'failed':
            case 'undelivered':
            case 'undeliv':
            default:
                $message->failed_at = Carbon::now();
                break;
        }

        //exception will cause carrier to retry status webhook, promising some level of consistency
        $message->save();

        return $this->respond();
    }

    protected function verify(Request $request): bool
    {
        return $this->driver->verifyHandlerRequest($request, $this->carrier);
    }

    protected function respond(): Response
    {
        return $this->driver->getHandlerResponse();
    }
}
