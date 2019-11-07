<?php

namespace Faravel;

use Faravel\Redis\Connections\PredisConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class RequestIdServiceProvider extends ServiceProvider
{
    const REQUEST_ID = 'REQUEST_ID';

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Request::requestId();
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        dd(static::REQUEST_ID);
        Request::macro('requestId', function() {

            return $this->server(static::REQUEST_ID);
        });
    }
}