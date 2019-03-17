<?php

namespace App;

use App\UsesHashids;
use App\Events\LogoCreated;
use App\Events\LogoDeletion;
use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    use UsesHashids;

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
