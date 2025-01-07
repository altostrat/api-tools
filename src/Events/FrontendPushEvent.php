<?php

namespace Altostrat\Tools\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FrontendPushEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use Queueable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        protected $user,
        protected $event,
        protected $data
    )
    {
        //
    }

    public function broadcastAs()
    {
        return $this->event;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user-' . $this->user);
    }
}