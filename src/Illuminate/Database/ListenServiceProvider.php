<?php

namespace Faravel\Illuminate\Database;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        Log::channel($this->log)->info($this->format($query));
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
     * @param $log
     */
    public function setLog($log)
    {
        $this->log = $log;
        return $this;
    }
}