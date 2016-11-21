<?php

namespace Panoscape\Tracker;
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

        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('tracker::tracker.created', ['context' => $this->getContextTypeName($context), 'name' => $context->getContextLabel()]),
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

        /*
        * Gets the context's altered values and tracks what had changed
        */
        $changes = $context->getDirty();
        $changed = [];
        foreach ($changes as $key => $value) {
            array_push($changed, ['key' => $key, 'old' => $context->getOriginal($key), 'new' => $context->$key]);
        }
            
        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('tracker::tracker.updating', ['context' => $this->getContextTypeName($context), 'name' => $context->getContextLabel()]),
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

        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('tracker::tracker.deleting', ['context' => $this->getContextTypeName($context), 'name' => $context->getContextLabel()]),
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

        $context->morphMany(Record::class, 'context')->create([
            'message' => trans('tracker::tracker.restored', ['context' => $this->getContextTypeName($context), 'name' => $context->getContextLabel()]),
            'agent_id' => $this->getCurrentAgentID(),
            'agent_type' => $this->getCurrentAgentType(),
            'performed_at' => time(),
        ]);
    }

    protected function getContextTypeName($context)
    {
        $class = class_basename($context);
        $key = 'tracker::tracker.'.snake_case($class);
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
            if(in_array('nobody', config('tracker.agent_ignore'))) {
                return false;
            }
        }
        elseif(in_array(get_class(Auth::user()), config('tracker.agent_ignore'))) {
            return false;
        }

        return in_array($action, config('tracker.operations'));
    }
}
