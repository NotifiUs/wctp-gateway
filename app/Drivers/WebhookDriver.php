<?php

namespace App\Drivers;

class WebhookDriver extends DriverFactory
{
    public int $maxMessageLength = 8192;

    public function queueOutbound($host, $carrier, $recipient, $message, $messageID, $reply_with){

    }

    public function handleInbound(){}
}
