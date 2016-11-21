<?php

namespace Panoscape\Tracker;
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
        if(!config('tracker.enabled')) {
            return;
        }

        if(in_array(App::environment(), config('tracker.env_ignore'))) {
            return;
        }

        if(App::runningInConsole() && !config('tracker.console')) {
            return;
        }

        if(App::runningUnitTests() && !config('tracker.unit_test')) {
            return;
        }

        static::observe(ContextObserver::class);
    }

    public abstract function getContextLabel();
}
