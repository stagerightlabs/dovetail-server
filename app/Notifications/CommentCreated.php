<?php

namespace App\Notifications;

use App\Page;
use App\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentCreated extends Notification
{
    use Queueable;

    /**
     * The comment that has been created
     *
     * @var Comment
     */
    public $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $comentatorName = $this->comment->commentator->name;
        $message = $this->getCommentMessage();

        return (new MailMessage)
                    ->subject("A new comment from {$comentatorName}")
                    ->line($message)
                    ->action('View Comment', '#');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'commentator' => $this->comment->commentator->name,
            'commentable' => $this->getCommentableName(),
            'message' => $this->getCommentMessage(),
        ];
    }

    /**
     * Determine the appropriate name for the commentable object
     *
     * @return string|null
     */
    protected function getCommentableName()
    {
        if ($this->comment->commentable instanceof Page) {
            return $this->comment->commentable->notebook->name;
        }

        return null;
    }

    /**
     * Generate a notification message for this comment
     *
     * @return string
     */
    public function getCommentMessage()
    {
        $comentatorName = $this->comment->commentator->name;
        $message = $comentatorName . ' has left you a comment';

        if ($commentableName = $this->getCommentableName()) {
            $message = $comentatorName . ' has commented on ' . $commentableName;
        }

        return $message;
    }
}
