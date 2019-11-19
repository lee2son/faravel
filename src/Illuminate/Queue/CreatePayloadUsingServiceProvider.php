<?php

namespace Faravel\Illuminate\Queue;

use Illuminate\Queue\Queue;
use Illuminate\Support\ServiceProvider;

class CreatePayloadUsingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            return [
                'time' => time(),
            ];
        });
    }
}