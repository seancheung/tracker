<?php

namespace Panoscape\Tracker;

use Illuminate\Support\ServiceProvider;

class TrackerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/tracker.php' => config_path('tracker.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/migrations/create_tracker_tables.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_tracker_tables.php')
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/lang', 'tracker');

        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/vendor/tracker'),
        ], 'translations');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/tracker.php', 'tracker');
    }
}