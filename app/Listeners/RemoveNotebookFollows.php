<?php

namespace App\Listeners;

use App\Events\NotebookDeletion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveNotebookFollows
{
    /**
     * Handle the event.
     *
     * @param  NotebookDeletion  $event
     * @return void
     */
    public function handle(NotebookDeletion $event)
    {
        $event->notebook->follows()->delete();
    }
}
