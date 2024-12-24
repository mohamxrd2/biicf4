<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CountdownTick implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $time;
    public function __construct($time)
    {
        $this->time = $time;
    }
    public function broadcastOn()
    {
        return new Channel('countdown');
    }
}
