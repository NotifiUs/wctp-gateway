<?php

namespace App\Models;

use App\Drivers\DriverFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    private $driver;

    private function setupDriver()
    {
        $driverFactory = new DriverFactory($this->carrier->api);
        $this->driver = $driverFactory->loadDriver();
    }

    public function carrier()
    {
        return $this->belongsTo('App\Models\Carrier');
    }

    public function getType(): string
    {
        $this->setupDriver();

        return $this->driver->getType($this->identifier);
    }

    public function getFriendlyType(): string
    {
        $this->setupDriver();

        return $this->driver->getFriendlyType($this->identifier);
    }

    public function provision(): bool
    {
        $this->setupDriver();

        return $this->driver->provisionNumber($this->carrier, $this->identifier);
    }

    public function getCarrierDetails(): array
    {
        $this->setupDriver();

        return $this->driver->getCarrierDetails($this->carrier, $this->identifier);
    }
}
