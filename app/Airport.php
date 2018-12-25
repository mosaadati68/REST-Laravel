<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [
        'iataCode', 'city', 'state'
    ];

    public function arrivalAirport()
    {
        return $this->hasMany('App\Flight', 'arrivalAirport_id');
    }

    public function departureAirport()
    {
        return $this->hasMany('App\Flight', 'departureAirport_id');
    }
}