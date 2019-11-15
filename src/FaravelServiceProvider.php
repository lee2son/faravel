<?php

namespace Faravel;

use Illuminate\Queue\Queue;
use Illuminate\Support\ServiceProvider;
use Faravel\Illuminate\Redis\ListenServiceProvider as ListenRedisServiceProvider;
use Faravel\Illuminate\Database\ListenServiceProvider as ListenSqlServiceProvider;;

class FaravelServiceProvider extends ServiceProvider
{
    protected $commands = [
        \Faravel\Console\BuildModel::class,
    ];

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        $this->registerProvider();
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/faravel.php', 'faravel');
        $this->commands($this->commands);

        $this->publishes([
            __DIR__ . '/../config/faravel.php' => config_path('faravel.php')
        ], 'faravel');

        if($defaultStringLength = config('faravel.default_string_length')) {
            \Illuminate\Database\Schema\Builder::defaultStringLength($defaultStringLength);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerProvider()
    {
        if(config('faravel.redis_log.enable')) {
            $this->app->register($this->app->resolveProvider(ListenRedisServiceProvider::class)->setChannel(config('faravel.redis_log.log')), true);
        }

        if(config('faravel.sql_log.enable')) {
            $this->app->register($this->app->resolveProvider(ListenSqlServiceProvider::class)->setChannel(config('faravel.sql_log.log')), true);
        }
    }
}