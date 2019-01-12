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

        // Remove the large thumbnail from storage
        if (Storage::disk('s3')->exists($event->document->large)) {
            Storage::disk('s3')->delete($event->document->large);
        }

        // Remove the small thumbnail from storage
        if (Storage::disk('s3')->exists($event->document->small)) {
            Storage::disk('s3')->delete($event->document->small);
        }
    }
}
