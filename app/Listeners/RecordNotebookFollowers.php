<?php

namespace App\Listeners;

use App\Notebook;
use App\Events\NotebookCreated;
use Illuminate\Support\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordNotebookFollowers
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
     * @param  NotebookCreated  $event
     * @return void
     */
    public function handle(NotebookCreated $event)
    {
        $members = $this->determineNewFollowers($event->notebook);

        $event->notebook->addFollower($members);
    }

    /**
     * Determine which users should automatically follow this new notebook
     *
     * @param Notebook $notebook
     * @return Collection
     */
    protected function determineNewFollowers(Notebook $notebook)
    {
        // Is this notebook owned by a single user?
        if ($notebook->owner_id) {
            return $notebook->owner;
        }

        // Is this notebook owned by a team?
        if ($notebook->team_id) {
            return $notebook->team->members;
        }

        // Otherwise we can assume that this notebook is owned by the entire org
        return $notebook->organization->users();
    }
}
