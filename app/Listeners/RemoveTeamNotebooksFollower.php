<?php

namespace App\Listeners;

use App\Events\TeamMemberRemoved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveTeamNotebooksFollower
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
     * @param  TeamMemberRemoved  $event
     * @return void
     */
    public function handle(TeamMemberRemoved $event)
    {
        $member = $event->user;

        $event->team->notebooks->each(function ($notebook) use ($member) {
            $notebook->removeFollower($member);
        });
    }
}
