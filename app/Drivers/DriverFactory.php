<?php

    namespace App\Drivers;

    use Exception;

    class DriverFactory
    {
        protected string $driver;

        protected array $supportedDrivers = [
            'twilio' => TwilioDriver::class,
            'thinq' => ThinQDriver::class,
        ];

        public function __construct( string $driver )
        {
            if(array_key_exists($driver, $this->supportedDrivers ) )
            {
                $this->driver = $driver;
            }
            else
            {
                throw new Exception('No compatible driver found');
            }
        }

        public function loadDriver(): TwilioDriver|ThinQDriver
        {
            return new $this->supportedDrivers[$this->driver]();
        }

    }



