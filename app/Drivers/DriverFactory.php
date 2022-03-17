<?php

    namespace App\Drivers;

    use Exception;

    class DriverFactory
    {
        protected string $driver;

        public static array $supportedDrivers = [
            'twilio' => TwilioSMSDriver::class,
            'thinq' => ThinQSMSDriver::class,
            'webhook' => WebhookSMSDriver::class,
            'sunwire' => SunwireSMSDriver::class,
        ];

        public function __construct( string $driver )
        {
            if( ! array_key_exists($driver, self::$supportedDrivers ) )
            {
                throw new Exception('No compatible driver found');
            }

            $this->driver = $driver;
        }

        public function loadDriver(): TwilioSMSDriver|ThinQSMSDriver|WebhookSMSDriver|SunwireSMSDriver
        {
            return new self::$supportedDrivers[$this->driver]();
        }
    }



