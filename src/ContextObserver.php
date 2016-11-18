<?php

namespace Panoscape\LaraTracker;
use Illuminate\Support\Facades\Auth;

class ContextObserver
{   
    /**
    * Listen to the Context created event.
    *
    * @param  mixed $context
    * @return void
    */
    public function created($context)
    {   
        if(!$this->filter('created')) return;

        $name = null;
        if($context instanceof Recordable) {
            $name = $context->getContextLabel();
        }
        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('laratracker::laratracker.created', ['context' => $this->getContextTypeName($context), 'name' => $name]),
            'agent_id' => $this->getCurrentAgentID(),
            'agent_type' => $this->getCurrentAgentType(),
            'performed_at' => time(),
        ]);
    }
    
    /**
    * Listen to the Context updating event.
    *
    * @param  mixed $context
    * @return void
    */
    public function updating($context)
    {
        if(!$this->filter('updating')) return;

        $name = null;
        if($context instanceof Recordable) {
            $name = $context->getContextLabel();
        }

        /*
        * Gets the context's altered values and tracks what had changed
        */
        $changes = $context->getDirty();
        $changed = [];
        foreach ($changes as $key => $value) {
            array_push($changed, ['key' => $key, 'old' => $context->getOriginal($key), 'new' => $context->$key]);
        }
            
        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('laratracker::laratracker.updating', ['context' => $this->getContextTypeName($context), 'name' => $name]),
            'meta' => $changed,
            'agent_id' => $this->getCurrentAgentID(),
            'agent_type' => $this->getCurrentAgentType(),
            'performed_at' => time(),
        ]);
    }
    
    /**
    * Listen to the Context deleting event.
    *
    * @param  mixed $context
    * @return void
    */
    public function deleting($context)
    {
        if(!$this->filter('deleting')) return;

        $name = null;
        if($context instanceof Recordable) {
            $name = $context->getContextLabel();
        }
        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('laratracker::laratracker.deleting', ['context' => $this->getContextTypeName($context), 'name' => $name]),
            'agent_id' => $this->getCurrentAgentID(),
            'agent_type' => $this->getCurrentAgentType(),
            'performed_at' => time(),
        ]);
    }
    
    /**
    * Listen to the Context restored event.
    *
    * @param  mixed $context
    * @return void
    */
    public function restored($context)
    {
        if(!$this->filter('restored')) return;

        $name = null;
        if($context instanceof Recordable) {
            $name = $context->getContextLabel();
        }
        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('laratracker::laratracker.restored', ['context' => $this->getContextTypeName($context), 'name' => $name]),
            'agent_id' => $this->getCurrentAgentID(),
            'agent_type' => $this->getCurrentAgentType(),
            'performed_at' => time(),
        ]);
    }

    protected function getContextTypeName($context)
    {
        $class = class_basename($context);
        $key = 'laratracker::laratracker.'.snake_case($class);
        $value =  trans($key);

        return $key == $value ? $class : $value;
    }

    protected function getCurrentAgentType()
    {
        return Auth::check() ? get_class(Auth::user()) : null;
    }

    protected function getCurrentAgentID()
    {
        return Auth::check() ? Auth::user()->id : null;
    }

    protected function filter($action)
    {
        if(!Auth::check()) {
            if(in_array('nobody', config('laratracker.agent_ignore'))) {
                return false;
            }
        }
        elseif(in_array(get_class(Auth::user()), config('laratracker.agent_ignore'))) {
            return false;
        }

        return in_array($action, config('laratracker.operations'));
    }
}
