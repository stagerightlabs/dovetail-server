<?php

namespace App;

use App\Model;
use App\Events\LogoCreated;
use App\Events\LogoDeletion;

class Logo extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => LogoCreated::class,
        'deleting' => LogoDeletion::class
    ];

    /**
     * Get all of the models owning logos
     */
    public function owner()
    {
        return $this->morphTo();
    }
}
