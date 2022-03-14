<?php

    namespace App\Drivers;

    use App\Carrier;
    use Carbon\Carbon;
    use App\EnterpriseHost;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;

    interface Driver
    {
        function getMaxMessageLength(): int;
        function getRequestInputUidKey(): string;
        function getRequestInputStatusKey(): string;
        function getRequestInputMessageKey(): string;
        function getHandlerResponse(): Response;
        function verifyHandlerRequest(Request $request, Carrier $carrier ): bool;
        function queueOutbound( EnterpriseHost $host, Carrier $carrier, $recipient, $message, $messageID, $reply_with): void;
        function saveInboundMessage(Request $request, int $carrier_id, int $number_id, int $enterprise_host_id, Carbon $submitted_at, string $reply_with = null): void;
    }



