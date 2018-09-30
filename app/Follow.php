<?php

namespace App;

use App\User;
use App\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Follow extends Model
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

    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['disabled_at', 'updated_at', 'completed_at'];

    /**
     * The user who is following this resource
     *
     * @return BelongsTo
     */
    public function follower()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The resource being followed
     *
     * @return MorphTo
     */
    public function followable()
    {
        return $this->morphTo();
    }
}
