<?php

    namespace App\Drivers;

    use Carbon\Carbon;
    use App\Models\Carrier;
    use App\Models\Message;
    use Illuminate\View\View;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use App\Models\EnterpriseHost;
    use Illuminate\Http\RedirectResponse;

    interface SMSDriver
    {
        /** Returns the maximum length message supported by carrier, such as 1600 */
        function getMaxMessageLength(): int;

        /** Returns the response for the Primary HTTP Handler, typically empty XML/JSON responses */
        function getHandlerResponse(): Response;

        /** Returns the message UID/ID key of HTTP response, i.e., 'SID' */
        function getRequestInputUidKey(): string;

        /** Returns the message status key of HTTP response, i.e., 'Status' */
        function getRequestInputStatusKey(): string;

        /** Returns the message key of HTTP response, i.e., 'Body' */
        function getRequestInputMessageKey(): string;

        /** Returns the short representation of numbers, such as PN for Phone Number, WH for Web Hook, MS for Messaging Service, etc. */
        function getType(string $identifier): string;

        /** Returns the friendly representation of numbers, such as Phone Number, Web Hook, Messaging Service, etc. */
        function getFriendlyType(string $identifier): string;

        /** Updates the number with $identifier hosted by the carrier to use this application (webhook configuration, IP whitelisting, etc.) */
        function provisionNumber(Carrier $carrier, string $identifier ): bool;

        /** Uses the primary/status handler (depending on carrier) to update status of sent messages (i.e., sent, delivered, failed, etc.) */
        function updateMessageStatus(Request|null $request, Carrier $carrier, Message $message): bool;

        /** Used to return carrier details to display when initially linking the accounts */
        function getCarrierDetails(Carrier $carrier, string $identifier): array;

        /** Verifies the handler request from the carrier has all the required inputs */
        function verifyHandlerRequest(Request $request, Carrier $carrier ): bool;

        /** Sends a message by placing the details in the outbound queue for processing */
        function queueOutbound( EnterpriseHost $host, Carrier $carrier, $recipient, $message, $messageID, $reply_with): void;

        /** Saves the inbound message into the database for statistics and reviewing against message status */
        function saveInboundMessage(Request $request, int $carrier_id, int $number_id, int $enterprise_host_id, Carbon $submitted_at, string $reply_with = null): void;

        /** Returns a list of numbers from the carrier that can be provisioned to use with this app. Not supported by all carriers. */
        function getAvailableNumbers(Request $request, Carrier $carrier): array;

        /** Returns a redirect or view after verifying the initial carrier setup details (i.e., twilio api key) */
        function verifyCarrierValidation( Request $request ): RedirectResponse | View;

        /** Creates the carrier instance after verifying the carrier validation */
        function createCarrierInstance( Request $request): RedirectResponse;

        /** Returns credentials that can be displayed to the user in the carrier web interface */
        function showCarrierCredentials( Carrier $carrier ): array;

        /** Returns an array of [url => '', title=> '' ] */
        function showCarrierImageDetails(): array;

        /** Returns a string to display on the Carrier quick-view page */
        function showCarrierDetails(Carrier $carrier ): string;

        /** Returns whether numbers can be auto provisioned or need to be manually added */
        function canAutoProvision(): bool;
    }



