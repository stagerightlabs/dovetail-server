<?php

namespace App\Listeners;

use App\Events\LogoCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\ProcessLogoImage;

class ScheduleLogoProcessing
{
    /**
     * Handle the event.
     *
     * @param  LogoCreated  $event
     * @return void
     */
    public function handle(LogoCreated $event)
    {
        ProcessLogoImage::dispatch($event->logo);
    }
}
