<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CountdownStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $code_unique;
    public $endTime;

    public function __construct($code_unique, $endTime)
    {
        $this->code_unique = $code_unique;
        $this->endTime = $endTime;
    }

    public function broadcastOn()
    {
        return new Channel('countdown');
    }

    public function broadcastToAll()
    {
        return true;
    }
}
