<?php

namespace App\Listeners;

use App\Page;
use App\Events\CommentCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CommentCreated as CommentNotification;

class SendCommentNotifications
{
    /**
     * Handle the event. Each commentable type might have slightly different
     * requirements
     *
     * @param  CommentCreated  $event
     * @return void
     */
    public function handle(CommentCreated $event)
    {
        // Who created this comment?
        $commentator = $event->comment->commentator;

        // Pages and Notebooks
        if ($event->comment->commentable instanceof Page) {
            $followers = $event->comment->commentable->notebook->getFollowers()
                ->filter(function ($user) use ($commentator) {
                    return $user->id != $commentator->id;
                });

            Notification::send($followers, new CommentNotification($event->comment));
        }
    }
}
