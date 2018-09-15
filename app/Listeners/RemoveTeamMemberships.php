<?php

namespace App\Listeners;

use App\Events\TeamDeletion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveTeamMemberships
{
    /**
     * Handle the event.
     *
     * @param  TeamDeletion  $event
     * @return void
     */
    public function handle(TeamDeletion $event)
    {
        // Remove all existing membership associations
        $event->team->members()->detach();
    }
}
