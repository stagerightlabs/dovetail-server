<?php

namespace App\Listeners;

use App\Events\PageDeletion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemovePageComments
{
    /**
     * Handle the event.
     *
     * @param  PageDeletion  $event
     * @return void
     */
    public function handle(PageDeletion $event)
    {
        $event->page->comments->each(function ($comment) {
            $comment->delete();
        });
    }
}
