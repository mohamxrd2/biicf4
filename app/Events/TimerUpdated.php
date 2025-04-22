<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $negotiation_id;
    public $seconds_remaining;
    public $timer_running;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($negotiation_id, $seconds_remaining, $timer_running)
    {
        $this->negotiation_id = $negotiation_id;
        $this->seconds_remaining = $seconds_remaining;
        $this->timer_running = $timer_running;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('negotiation');
    }
}
