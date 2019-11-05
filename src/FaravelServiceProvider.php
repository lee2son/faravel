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

class FaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        $this->redisExtend();
        $this->listenSql();
        $this->listenRedis();
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/faravel.php', 'faravel');
        $this->registerCommand();

        $this->publishes([
            __DIR__ . '/../config/faravel.php' => config_path('faravel.php')
        ], 'faravel');
    }

    /**
     * 注册命令
     */
    protected function registerCommand()
    {
        $this->commands(\Faravel\Console\Commands\BuildModel::class);
    }

    /**
     * 监听 sql
     */
    protected function listenSql()
    {
        if(!config('faravel.listen_sql.enable')) {
            return;
        }

        DB::connection('v2')->listen(function (QueryExecuted $query) {
            $sql = sql($query->sql, $query->bindings);
            $text = sprintf('sql:%s %.03fms -> %s', $query->connectionName, $query->time, $sql);
            Log::channel(config('faravel.listen_sql.log'))->info($text);
        });
    }

    /**
     * 监听 redis
     */
    protected function listenRedis()
    {
        if(!config('faravel.listen_redis.enable')) {
            return;
        }

        Redis::enableEvents();

        Redis::listen(function(CommandExecuted $cmd) {
            $text = sprintf("redis:%s %.03fms -> %s %s", $cmd->connectionName, $cmd->time, $cmd->command, implode(' ', $cmd->parameters));
            Log::channel(config('faravel.listen_redis.log'))->info($text);
        });
    }

    /**
     * 扩展 predis
     */
    protected function redisExtend()
    {
        Redis::extend('predis', function() {
            return new \Faravel\Redis\Connectors\PredisConnector();
        });
    }
}