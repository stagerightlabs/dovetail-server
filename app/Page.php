<?php

namespace App;

use App\Model;
use App\Notebook;
use App\Events\PageDeletion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Page extends Model
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
        'deleting' => PageDeletion::class
    ];

    /**
     * The notebook that owns this page
     *
     * @return BelongsTo
     */
    public function notebook()
    {
        return $this->belongsTo(Notebook::class);
    }

    /**
     * Get all of this page's comments
     *
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    /**
     * Get all of this page's documents
     *
     * @return MorphMany
     */
    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }
}
