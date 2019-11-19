<?php

namespace Faravel;

use Faravel\Illuminate\Database\ListenServiceProvider as ListenSqlServiceProvider;
use Faravel\Illuminate\Foundation\Http\ListenResponseHandledServiceProvider;
use Faravel\Illuminate\Redis\ListenServiceProvider as ListenRedisServiceProvider;
use Faravel\Illuminate\Routing\ListenRequestServiceProvider;
use Faravel\Illuminate\Routing\ListenRouteMatchedServiceProvider;
use Illuminate\Support\ServiceProvider;

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
        $this->bootListenSql();
        $this->bootListenRedis();
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

    protected function bootListenSql()
    {
        $config = config('faravel.sql_log');
        if(!$config['enable']) {
            return;
        }

        /**
         * @var ListenSqlServiceProvider
         */
        $provider = $this->app->resolveProvider(ListenSqlServiceProvider::class);
        $provider->setLog($config['log']);

        $this->app->register($provider);
    }

    protected function bootListenRedis()
    {
        $config = config('faravel.redis_log');
        if(!$config['enable']) {
            return;
        }

        /**
         * @var ListenRedisServiceProvider
         */
        $provider = $this->app->resolveProvider(ListenRedisServiceProvider::class);
        $provider->setLog($config['log']);

        $this->app->register($provider);
    }

//    protected function bootListenRequest()
//    {
//        $config = config('faravel.request_log');
//        if(!$config['enable']) {
//            return;
//        }
//
//        /**
//         * @var ListenRequestServiceProvider
//         */
//        $provider = $this->app->resolveProvider(ListenRouteMatchedServiceProvider::class);
//        $provider->setLog($config['log']);
//        $provider->setWhat($config['what']);
//        $provider->setPrefix($config['prefix']);
//
//        $this->app->register($provider);
//    }
//
//    protected function bootListenResponse()
//    {
//        $config = config('faravel.response_log');
//        if(!$config['enable']) {
//            return;
//        }
//
//        /**
//         * @var ListenRequestServiceProvider
//         */
//        $provider = $this->app->resolveProvider(ListenResponseHandledServiceProvider::class);
//        $provider->setLog($config['log']);
////        $provider->setWhat($config['what']);
////        $provider->setPrefix($config['prefix']);
//
//        $this->app->register($provider);
//    }
//
//    protected function registerRouteMacro()
//    {
//        \Illuminate\Routing\Route::macro('log', function($log) {
//            $this->action['log'] = $log;
//            return $this;
//        });
//    }
}