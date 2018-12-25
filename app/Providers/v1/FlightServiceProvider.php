<?php

namespace App\Providers\v1;

use App\Services\v1\FlightService;
use Illuminate\Support\ServiceProvider;

class FlightServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FlightService::class, function ($app) {
            return new FlightService();
        });
    }
}
