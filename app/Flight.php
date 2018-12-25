<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    public function arrivingFlights()
    {
        return $this->belongsTo('App\Airport', 'arrivalAirport_id');
    }

    public function departingFlights()
    {
        return $this->belongsTo('App\Airport', 'depatureAirport_id');
    }

    public function passengers()
    {
        return $this->belongsToMany('App\Customer', 'flight_customer');
    }
}
