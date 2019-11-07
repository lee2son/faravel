<?php

namespace Faravel;

use Faravel\Redis\Connections\PredisConnection;
use Illuminate\Contracts\Redis\Connector;
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

class RedisExtendServiceProvider extends ServiceProvider
{
    const CONNECTOR = \Faravel\Redis\Connectors\PredisConnector::class;

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Redis::extend('predis', function() {
            return $this->getConnector();
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
     * 获取一个 predis-connector
     * @return Connector
     */
    protected function getConnector(): Connector
    {
        $connector = static::CONNECTOR;
        return new $connector();
    }
}