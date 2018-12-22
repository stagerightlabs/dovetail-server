<?php

namespace App\Providers;

use App\Events\LogoCreated;
use App\Events\UserCreated;
use App\Events\LogoDeletion;
use App\Events\TeamDeletion;
use App\Events\CommentCreated;
use App\Events\DocumentCreated;
use App\Events\NotebookCreated;
use App\Events\TeamMemberAdded;
use App\Events\DocumentDeletion;
use App\Events\NotebookDeletion;
use App\Events\TeamMemberRemoved;
use Illuminate\Support\Facades\Event;
use App\Listeners\RemovePageComments;
use App\Listeners\RemovePageDocuments;
use Illuminate\Auth\Events\Registered;
use App\Listeners\ReassignTeamNotebooks;
use App\Listeners\RemoveNotebookFollows;
use App\Listeners\RemoveTeamMemberships;
use App\Listeners\ScheduleLogoProcessing;
use App\Listeners\AddTeamNotebookFollower;
use App\Listeners\RecordNotebookFollowers;
use App\Listeners\SendCommentNotifications;
use App\Listeners\RemoveDocumentFromStorage;
use App\Listeners\GenerateDocumentThumbnails;
use App\Listeners\RemoveLogoFilesFromStorage;
use App\Listeners\RemoveTeamNotebooksFollower;
use App\Listeners\AssignDefaultUserPermissions;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreated::class => [
            AssignDefaultUserPermissions::class,
        ],
        TeamMemberAdded::class => [
            AddTeamNotebookFollower::class,
        ],
        TeamMemberRemoved::class => [
            RemoveTeamNotebooksFollower::class,
        ],
        TeamDeletion::class => [
            RemoveTeamMemberships::class,
            ReassignTeamNotebooks::class,
        ],
        NotebookCreated::class => [
            RecordNotebookFollowers::class,
        ],
        NotebookDeletion::class => [
            RemoveNotebookFollows::class,
        ],
        PageDeletion::class => [
            RemovePageComments::class,
            RemovePageDocuments::class,
        ],
        LogoCreated::class => [
            ScheduleLogoProcessing::class,
        ],
        LogoDeletion::class => [
            RemoveLogoFilesFromStorage::class,
        ],
        CommentCreated::class => [
            SendCommentNotifications::class,
        ],
        DocumentCreated::class => [
            GenerateDocumentThumbnails::class
        ],
        DocumentDeletion::class => [
            RemoveDocumentFromStorage::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
