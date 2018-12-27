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
        if ($notebook->user_id) {
            return $notebook->user;
        }

        // Is this notebook owned by a team?
        if ($notebook->team_id) {
            return $notebook->team->members;
        }

        // Otherwise we can assume that this notebook is owned by the entire org
        // Currently, we don't want automatic organization wide follows.
        // To enable that option, use this line:
        // return $notebook->organization->users();

        return collect();
    }
}
