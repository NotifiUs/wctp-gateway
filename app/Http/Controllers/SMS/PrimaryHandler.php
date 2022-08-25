<?php

namespace App\Http\Controllers\SMS;

use App\Drivers\DriverFactory;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\EnterpriseHost;
use App\Models\Number;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\NoReturn;

class PrimaryHandler extends Controller
{
    private $driver;

    private Carrier|null $carrier = null;

    #[NoReturn]
    public function __invoke(Request $request, string $identifier): Response|JsonResponse
    {
        $number = Number::where('enabled', 1)->where('identifier', $identifier)->first();

        if ($number === null) {
            return $this->fail('There is no number here');
        }

        $this->carrier = Carrier::where('enabled', 1)->where('id', $number->carrier_id)->first();

        if ($this->carrier === null) {
            return $this->fail('This isn\'t the carrier you are looking for');
        }

        try {
            $driverFactory = new DriverFactory($this->carrier->api);
            $this->driver = $driverFactory->loadDriver();
        } catch (Exception $e) {
            return $this->fail('Unable to load SMS driver');
        }

        $host = EnterpriseHost::where('enabled', 1)->where('id', $number->enterprise_host_id)->first();

        if ($host === null) {
            return $this->respond();
        }

        if (! $this->driver->verifyHandlerRequest($request, $this->carrier)) {
            return $this->respond();
        }

        $reply_with = null;

        $reply_phrase = preg_match('/\b\d+( ?ok(ay)?)\b/i', $request->input($this->driver->getRequestInputMessageKey()), $matches);
        if ($reply_phrase && isset($matches[0])) {
            $reply_with = str_replace(['okay', 'ok'], '', $matches[0]);
        }

        $this->driver->saveInboundMessage(
            $request,
            $this->carrier->id,
            $number->id,
            $host->id,
            Carbon::now(),
            $reply_with
        );

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

    protected function fail(string $error = null): JsonResponse|Response
    {
        return response()->json(['error' => 400, 'desc' => $error ?? 'bad request'], 400, [], JSON_PRETTY_PRINT);
    }
}
