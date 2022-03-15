<?php

    namespace App\Drivers;

    use App\Models\Carrier;
    use App\Models\Message;
    use Carbon\Carbon;
    use App\Models\EnterpriseHost;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;

    interface Driver
    {
        function getMaxMessageLength(): int;
        function getHandlerResponse(): Response;
        function getRequestInputUidKey(): string;
        function getRequestInputStatusKey(): string;
        function getRequestInputMessageKey(): string;
        function getType(string $identifier): string;
        function getFriendlyType(string $identifier): string;
        function provisionNumber(Carrier $carrier, string $identifier ): bool;
        function updateMessageStatus(Carrier $carrier, Message $message): bool;
        function getCarrierDetails(Carrier $carrier, string $identifier): array;
        function verifyHandlerRequest(Request $request, Carrier $carrier ): bool;
        function queueOutbound( EnterpriseHost $host, Carrier $carrier, $recipient, $message, $messageID, $reply_with): void;
        function saveInboundMessage(Request $request, int $carrier_id, int $number_id, int $enterprise_host_id, Carbon $submitted_at, string $reply_with = null): void;

    }



