<?php

namespace App\Listeners;

use App\Events\DocumentDeletion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveDocumentFromStorage
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
     * Handle the event.
     *
     * @param  DocumentDeletion  $event
     * @return void
     */
    public function handle(DocumentDeletion $event)
    {
        // Remove the original from storage
        if (Storage::disk('s3')->exists($event->document->original)) {
            Storage::disk('s3')->delete($event->document->original);
        }

        // Remove the standard thumbnail from storage
        if (Storage::disk('s3')->exists($event->document->standard)) {
            Storage::disk('s3')->delete($event->document->standard);
        }

        // Remove the thumbnail thumbnail from storage
        if (Storage::disk('s3')->exists($event->document->thumbnail)) {
            Storage::disk('s3')->delete($event->document->thumbnail);
        }

        // Remove the icon thumbnail from storage
        if (Storage::disk('s3')->exists($event->document->icon)) {
            Storage::disk('s3')->delete($event->document->icon);
        }
    }
}
