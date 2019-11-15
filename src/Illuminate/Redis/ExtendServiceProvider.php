<?php

namespace Faravel\Illuminate\Redis;

use Faravel\Illuminate\Redis\Connectors\PredisConnector;
use Illuminate\Contracts\Redis\Connector;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class ExtendServiceProvider extends ServiceProvider
{
    const PREDIS_CONNECTOR = PredisConnector::class;

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        $provider = $this;
        Redis::extend('predis', function() use($provider) {
            return $provider->createPredisConnector();
        });
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
    }

    /**
     * Create a predis-connector
     * @return Connector
     */
    public function createPredisConnector(): Connector
    {
        $connector = static::PREDIS_CONNECTOR;
        return new $connector();
    }
}