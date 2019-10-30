<?php

namespace Faravel;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class FaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        $this->bootListenSql();
        $this->bootListenRedis();
        $this->bootRedisExtend();
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/faravel.php', 'faravel');
        $this->registerCommand();
    }

    /**
     * 注册命令
     */
    protected function registerCommand()
    {
        $this->commands(\Faravel\Console\Commands\BuildModel::class);
    }

    /**
     * 监听SQL，记录到日志
     */
    protected function bootListenSql()
    {
        if(config('faravel.listen_sql.enable')) {
            DB::listen(function (QueryExecuted $query) {
                $sql = sql($query->sql, $query->bindings);
                $text = sprintf('%s %sms:%s', $query->connectionName, $query->time, $sql);
                Log::channel(config('faravel.listen_sql.log'))->info($text);
            });
        }
    }

    /**
     * 监听 redis
     */
    protected function bootListenRedis()
    {
        foreach(config('faravel.listen_redis') as $connection => $log)
        {
            app('redis')->connection($connection)->enableEvents();
            app('redis')->connection($connection)->listen(function(CommandExecuted $cmd) use($log) {
                $text = sprintf("%s %dms:%s %s", $cmd->connectionName, $cmd->time, $cmd->command, implode(' ', $cmd->parameters));
                Log::channel($log)->info($text);
            });
        }
    }

    /**
     * 扩展 predis
     */
    protected function bootRedisExtend()
    {
        app('redis')->extend('predis', function() {
            return new \Faravel\Redis\Connectors\PredisConnector();
        });
    }
}