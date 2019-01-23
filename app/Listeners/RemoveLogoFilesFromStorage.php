<?php

namespace App\Listeners;

use App\Events\LogoDeletion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveLogoFilesFromStorage
{
    /**
     * Handle the event.
     *
     * @param  LogoDeletion  $event
     * @return void
     */
    public function handle(LogoDeletion $event)
    {
        Storage::disk('s3')->delete([
            $event->logo->original,
            $event->logo->standard,
            $event->logo->thumbnail,
            $event->logo->icon,
        ]);
    }
}
