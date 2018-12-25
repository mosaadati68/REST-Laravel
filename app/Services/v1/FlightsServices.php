<?php

namespace App\Services\v1;

use App\Airport;
use App\Flight;
use http\Env\Request;
use function Symfony\Component\Debug\Tests\testHeader;

class FlightService
{
    protected $supportedIncludes = [
        'arrivalAirport' => 'arrival',
        'departureAirport' => 'departure'
    ];

    protected $clauseProperties = [
        'status',
        'flightNumber'
    ];

    public function getFlights($parameters)
    {
        if (empty($parameters)) {
            return $this->filterFlights(Flight::all());
        }
        $withKeys = $this->getWithKeys($parameters);
        $whereClauses = $this->getWhereClause($parameters);
        $flights = Flight::with($withKeys)->where($whereClauses)->get();

        return $this->filterFlights($flights, $withKeys);
    }

    public function createFlight($req)
    {
        $arrivalAirport = $req->input('arrival.iataCode');
        $departureAirport = $req->input('departure.iataCode');
        $airports = Airport::whereIn('iataCode',
            [
                $arrivalAirport,
                $departureAirport
            ])->get();
        $codes = [];

        foreach ($airports as $port) {
            $codes[$port->iataCode] = $port->id;
        }
        $flight = new Flight();
        $flight->flightNumber = $req->input('flightNumber');
        $flight->status = $req->input('status');
        $flight->arrivalAirport_id = $codes[$arrivalAirport];
        $flight->arrivalDateTime = $req->input('arrival.datetime');
        $flight->departureAirport_id = $codes[$departureAirport];
        $flight->departureDateTime = $req->input('departure.datetime');

        $flight->save();

        return $this->filterFlights($flight);
    }

    public function updateFlight($req, $flightNumber)
    {
        $flight = Flight::where('flightNumber',$flightNumber)->firstOrFail();
        $arrivalAirport = $req->input('arrival.iataCode');
        $departureAirport = $req->input('departure.iataCode');
        $airports = Airport::whereIn('iataCode',
            [
                $arrivalAirport,
                $departureAirport
            ])->get();
        $codes = [];

        foreach ($airports as $port) {
            $codes[$port->iataCode] = $port->id;
        }
        $flight->flightNumber = $req->input('flightNumber');
        $flight->status = $req->input('status');
        $flight->arrivalAirport_id = $codes[$arrivalAirport];
        $flight->arrivalDateTime = $req->input('arrival.datetime');
        $flight->departureAirport_id = $codes[$departureAirport];
        $flight->departureDateTime = $req->input('departure.datetime');

        $flight->save();

        return $this->filterFlights($flight);
    }

    public function deleteFlight($flightNumber)
    {
        $flight = Flight::where('flightNumber',$flightNumber)->firstOrFail();

        $flight->delete();
    }

    public function filterFlights($flights, $keys = [])
    {
        $data = [];
        $entry = [];
        foreach ($flights as $flight) {
            $entry = [
                'flightNumber' => $flight->flightNumber,
                'status' => $flight->status,
                'href' => route('flights.show', ['id' => $flight->flightNumber])
            ];

            if (in_array('arrivalAirport', $keys)) {
                $entry['arrival'] =
                    [
                        'datetime' => $flight->arrivalDateTime,
                        'iataCode' => $flight->arrivalAirport->iataCode,
                        'city' => $flight->arrivalAirport->city,
                        'state' => $flight->arrivalAirport->state,
                    ];
            }
            if (in_array('departureAirport', $keys)) {
                $entry['departure'] =
                    [
                        'datetime' => $flight->departureDateTime,
                        'iataCode' => $flight->departureAirport->iataCode,
                        'city' => $flight->departureAirport->city,
                        'state' => $flight->departureAirport->state,
                    ];
            }
            $data[] = $entry;
        }
        return $data;
    }

    protected function getWithKeys($parameters)
    {
        $withKeys = [];
        if (isset($parameters['include'])) {
            $includeParams = explode(',', $parameters['include']);
            $includes = array_intersect($this->supportedIncludes, $includeParams);
            $withKeys = array_keys($includes);
        }
        return $withKeys;
    }

    protected function getWhereClause($parameters)
    {
        $clause = [];
        foreach ($this->clauseProperties as $prop) {
            if (in_array($prop, array_keys($parameters))) {
                $clause[$prop] = $parameters[$prop];
            }
        }
        return $clause;
    }
}