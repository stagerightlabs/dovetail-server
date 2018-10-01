<?php

namespace App;

use App\Model;
use App\Events\CommentCreated;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
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
        'created' => CommentCreated::class,
    ];

    /**
     * The model that is being commented on
     *
     * @return MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * The user that created this comment
     *
     * @return BelongsTo
     */
    public function commentator()
    {
        return $this->belongsTo(User::class, 'commentator_id')->withTrashed();
    }
}
