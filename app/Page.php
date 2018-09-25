<?php

namespace App;

use App\Model;
use App\Notebook;
use App\Events\PageDeletion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
