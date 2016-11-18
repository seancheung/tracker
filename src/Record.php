<?php

namespace Panoscape\Laratracker;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['performed_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array'
    ];

    /**
    * The attributes that are not mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('laratracker.records_table'));
        parent::__construct($attributes);
    }

    /**
     * Get the agent of this record
     */
    public function agent()
    {
        return $this->morphTo();
    }

    /**
     * Returns whether or not a agent type/id are present.
     *
     * @return bool
     */
    public function hasAgent()
    {
        return !empty($this->agent_type) && !empty($this->agent_id);
    }

    /**
     * Get the context of this record
     */
    public function context()
    {
        return $this->morphTo();
    }
}
