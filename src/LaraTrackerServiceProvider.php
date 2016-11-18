<?php

namespace Panoscape\LaraTracker;

use Illuminate\Support\ServiceProvider;

class LaraTrackerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laratracker.php' => config_path('laratracker.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations')
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/lang', 'laratracker');

        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/vendor/laratracker'),
        ], 'translations');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/laratracker.php', 'laratracker');
    }
}