<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use App\Events\DocumentCreated;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateDocumentThumbnails
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.  For now we will handle image processing inline and
     * not as a queued job.
     *
     * @param  DocumentCreated  $event
     * @return void
     */
    public function handle(DocumentCreated $event)
    {
        // We will skip thumbnail generation if the document is a PDF
        if ($event->document->mimetype == 'application/pdf') {
            return;
        }

        // Read the file contents from storage
        $imageContents = Storage::disk('s3')->get($event->document->original);
        $pathinfo = pathinfo($event->document->original);

        // Create the 'large' thumbnail
        $image = Image::make($imageContents);
        $image->resize(150, 150)->encode();
        $event->document->large = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($event->document->large, (string)$image);

        // Create the 'small' thumbnail
        $image = Image::make($imageContents);
        $image->resize(50, 50)->encode();
        $event->document->small = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($event->document->small, (string)$image);

        // All set
        $event->document->save();
    }
}
