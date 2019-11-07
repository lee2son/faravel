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

class ListenSqlServiceProvider extends ServiceProvider
{
    /**
     * @var string log channel
     */
    public $channel = "";

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        DB::listen(function (QueryExecuted $query) {
            $this->listen($query);
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
     * 监听函数
     * @param QueryExecuted $query
     */
    protected function listen(QueryExecuted $query)
    {
        Log::channel($this->channel)->info($this->format($query));
    }

    /**
     * 日志格式
     * @param QueryExecuted $query
     * @return string
     */
    protected function format(QueryExecuted $query)
    {
        $sql = sql($query->sql, $query->bindings);
        return sprintf('sql:%s %.03fms -> %s', $query->connectionName, $query->time, $sql);
    }

    /**
     * 设置记录日志的channel
     * @param $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }
}