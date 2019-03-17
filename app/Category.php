<?php

namespace App;

use App\User;
use App\UsesHashids;
use App\Organization;
use App\Events\CategoryDeletion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
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
        'deleting' => CategoryDeletion::class
    ];

    /**
     * The organization that owns this category
     *
     * @return BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The user that created this category
     *
     * @return BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
