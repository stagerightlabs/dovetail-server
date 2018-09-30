<?php

namespace App\Listeners;

use App\Events\TeamDeletion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReassignTeamNotebooks
{
    /**
     * Handle the event.
     *
     * @param  TeamDeletion  $event
     * @return void
     */
    public function handle(TeamDeletion $event)
    {
        $event->team->notebooks()->update(['team_id' => null]);
    }
}
