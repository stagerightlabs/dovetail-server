<?php

namespace App;

use App\Model;
use App\Events\TeamDeletion;

class Team extends Model
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
        'deleting' => TeamDeletion::class
    ];
}
