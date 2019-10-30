<?php

namespace Faravel;

use Illuminate\Database\Events\QueryExecuted;
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

    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/faravel.php', 'faravel');
        $this->registerPredisex();
        $this->registerCommand();
        $this->listenSql();
    }

    /**
     * 扩展 predis
     */
    protected function registerPredisex()
    {
        app('redis')->extend('predis', function() {
            return new \Faravel\Redis\Connectors\PredisExConnector();
        });
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
    protected function listenSql()
    {
        if(config('faravel.listen_sql.enable')) {
            DB::listen(function (QueryExecuted $query) {
                $sql = sql($query->sql, $query->bindings);
                $text = sprintf('%s %sms %s', $query->connectionName, $query->time, $sql);
                Log::channel(config('faravel.listen_sql.log'))->info($text);
            });
        }
    }
}