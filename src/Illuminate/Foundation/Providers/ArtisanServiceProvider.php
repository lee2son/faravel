<?php

namespace Faravel\Illuminate\Foundation\Providers;

use Faravel\Illuminate\Queue\Console\WorkCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
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
        $this->app->singleton('command.queue.work', function ($app) {
            return new WorkCommand($app['queue.worker']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.queue.work'
        ];
    }
}