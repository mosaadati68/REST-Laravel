<?php

use Faker\Generator as Faker;

$factory->define(App\Airport::class, function (Faker $faker) {
    return [
        'iatacode' => str_random(3),
        'city' => $faker->city,
        'state' => $faker->stateAbbr
    ];
});

$factory->define(App\Flight::class, function (Faker $faker) {
    $flightHours = $faker->numberBetween(1, 5);
    $flightTime = new DateInterval('PT' . $flightHours . 'H');
    $arrival = $faker->dateTime;
    $depart = clone $arrival;
    $depart->sub($flightTime);
    return [
        'flightNumber' => str_random(3) . $faker->unique()->randomNumber(5),
        'arrivalAirport_id' => $faker->numberBetween(1, 5),
        'arrivalDateTime' => $arrival,
        'depatureAirport_id' => $faker->numberBetween(1, 5),
        'depatureDateTime' => $depart,
        'status' => $faker->boolean ? "online" : "delayed"

    ];
});

$factory->define(App\Customer::class, function (Faker $faker) {
    return [
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName
    ];
});