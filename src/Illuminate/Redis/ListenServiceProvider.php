<?php

namespace Faravel\Illuminate\Redis;

use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class ListenServiceProvider extends ServiceProvider
{
    /**
     * @var string log channel
     */
    public $log = "";

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
        Log::channel($this->log)->info($this->format($cmd));
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
    public function setLog($log)
    {
        $this->log = $log;
        return $this;
    }
}