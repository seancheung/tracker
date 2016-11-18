<?php

namespace Panoscape\LaraTracker;
use App;

trait Context
{
    /**
     * Get all of the context's records.
     */
    public function records()
    {
        return $this->morphMany(Record::class, 'context');
    }

    public static function bootContext()
    {
        if(!config('laratracker.enabled')) {
            return;
        }

        if(in_array(App::environment(), config('laratracker.env_ignore'))) {
            return;
        }

        if(App::runningInConsole() && !config('laratracker.console')) {
            return;
        }

        if(App::runningUnitTests() && !config('laratracker.unit_test')) {
            return;
        }

        static::observe(ContextObserver::class);
    }
}
