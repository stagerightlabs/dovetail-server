<?php

namespace App;

use App\User;
use App\UsesHashids;
use App\Events\DocumentCreated;
use App\Events\DocumentDeletion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
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
        'created' => DocumentCreated::class,
        'deleting' => DocumentDeletion::class
    ];

    /**
     * The model that owns this document
     *
     * @return MorphTo
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    /**
     * Remove the document's thumbnails from storage if they exist
     *
     * @return void
     */
    public function removeThumbnails()
    {
        if (Storage::disk('s3')->exists($this->thumbnail)) {
            Storage::disk('s3')->delete($this->standard);
            Storage::disk('s3')->delete($this->thumbnail);
            Storage::disk('s3')->delete($this->icon);
        }
    }

    /**
     * Is this document a PDF?
     *
     * @return boolean
     */
    public function isPdf()
    {
        return $this->mimetype == 'application/pdf';
    }

    /**
     * The user that uploaded this document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
