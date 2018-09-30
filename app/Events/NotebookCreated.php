<?php

namespace App\Events;

use App\Notebook;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotebookCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The notebook that has been created
     *
     * @var Notebook
     */
    public $notebook;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Notebook $notebook)
    {
        $this->notebook = $notebook;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
