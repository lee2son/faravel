<?php

namespace Faravel;

use Faravel\Redis\Connections\PredisConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Http\Request;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class FaravelServiceProvider extends ServiceProvider
{
    protected $commands = [
        \Faravel\Console\Commands\BuildModel::class
    ];

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
        $this->commands($this->commands);

        $this->publishes([
            __DIR__ . '/../config/faravel.php' => config_path('faravel.php')
        ], 'faravel');

        if($defaultStringLength = config('faravel.default_string_length')) {
            \Illuminate\Database\Schema\Builder::defaultStringLength($defaultStringLength);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerProvider();
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