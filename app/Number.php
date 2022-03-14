<?php

namespace App;

use Exception;
use App\Drivers\DriverFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    private $driver;

    public function __construct()
    {
        parent::__construct();

        try{
            $driverFactory = new DriverFactory( $this->carrier->api );
            $this->driver = $driverFactory->loadDriver();
        }
        catch( Exception $e ) {}
    }

    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function getType(): string
    {
        return $this->driver->getType( $this->identifier );
    }

    public function getFriendlyType(): string
    {
        return $this->driver->getFriendlyType( $this->identifier );
    }

    public function provision(): bool
    {
        return $this->driver->provisionNumber($this->carrier, $this->identifier);
    }

    public function getCarrierDetails(): array
    {
        return $this->driver->getCarrierDetails( $this->carrier, $this->identifier );
    }
}
