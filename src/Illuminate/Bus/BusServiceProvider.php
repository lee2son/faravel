<?php

namespace Faravel\Illuminate\Bus;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Contracts\Bus\QueueingDispatcher as QueueingDispatcherContract;
use Illuminate\Contracts\Container\Container;

/**
 * Class BusServiceProvider
 * @package Faravel\Illuminate\Bus
 */
class BusServiceProvider extends \Illuminate\Bus\BusServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Illuminate\Bus\Dispatcher::class, function ($app) {
            return new Dispatcher($app, function ($connection = null) use ($app) {
                return $app[QueueFactoryContract::class]->connection($connection);
            });
        });

        $this->app->alias(
            \Illuminate\Bus\Dispatcher::class, DispatcherContract::class
        );

        $this->app->alias(
            \Illuminate\Bus\Dispatcher::class, QueueingDispatcherContract::class
        );
    }
}
