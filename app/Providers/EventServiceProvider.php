<?php

namespace App\Providers;

use App\Events\LogoCreated;
use App\Events\UserCreated;
use App\Events\LogoDeletion;
use App\Events\TeamDeletion;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\RemoveTeamMemberships;
use App\Listeners\ScheduleLogoProcessing;
use App\Listeners\RemoveLogoFilesFromStorage;
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
            AssignDefaultUserPermissions::class
        ],
        TeamDeletion::class => [
            RemoveTeamMemberships::class
        ],
        LogoCreated::class => [
            ScheduleLogoProcessing::class
        ],
        LogoDeletion::class => [
            RemoveLogoFilesFromStorage::class
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
