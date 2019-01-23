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

        // Create the 'thumbnail' image
        $image = Image::make($imageContents);
        $image->resize(150, 150)->encode();
        $event->document->thumbnail = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($event->document->thumbnail, (string)$image);

        // Create the 'icon' image
        $image = Image::make($imageContents);
        $image->resize(50, 50)->encode();
        $event->document->icon = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($event->document->icon, (string)$image);

        // Create the 'standard' image
        $image = Image::make($imageContents);
        $width = null;
        $height = null;
        if ($image->height() > $image->width()) {
            $height = 800;
        } else {
            $width = 800;
        }
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode();
        $event->document->standard = $pathinfo['dirname'] . '/' . Str::random(40) . '.' . $pathinfo['extension'];
        Storage::disk('s3')->put($event->document->standard, (string)$image);

        // All set
        $event->document->save();
    }
}
