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

class ListenRedisServiceProvider extends ServiceProvider
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
        Redis::enableEvents();

        Redis::listen(function(CommandExecuted $cmd) {
            $this->listen($cmd);
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
     * @param CommandExecuted $cmd
     */
    protected function listen(CommandExecuted $cmd)
    {
        Log::channel($this->channel)->info($this->format($cmd));
    }

    /**
     * 日志格式
     * @param CommandExecuted $cmd
     * @return string
     */
    protected function format(CommandExecuted $cmd)
    {
        return sprintf("redis:%s %.03fms -> %s %s", $cmd->connectionName, $cmd->time, $cmd->command, implode(' ', $cmd->parameters));
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