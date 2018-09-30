<?php

namespace App\Listeners;

use App\Events\TeamMemberAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddTeamNotebookFollower
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
     * @param  TeamMemberAdded  $event
     * @return void
     */
    public function handle(TeamMemberAdded $event)
    {
        $member = $event->user;

        $event->team->notebooks()->each(function ($notebook) use ($member) {
            $notebook->addFollower($member);
        });
    }
}
