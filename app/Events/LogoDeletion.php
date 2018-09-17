<?php

namespace App\Events;

use App\Logo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogoDeletion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The logo being removed
     *
     * @var Logo
     */
    public $logo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Logo $logo)
    {
        $this->logo = $logo;
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
