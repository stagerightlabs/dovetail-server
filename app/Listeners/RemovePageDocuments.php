<?php

namespace App\Listeners;

use App\Events\PageDeletion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemovePageDocuments
{
    /**
     * Handle the event.
     *
     * @param  PageDeletion  $event
     * @return void
     */
    public function handle(PageDeletion $event)
    {
        $event->page->documents->each(function ($document) {
            $document->delete();
        });
    }
}
