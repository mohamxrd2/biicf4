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
    public $time;
    public $code_unique;

    public function __construct($time, $code_unique)
    {
        $this->time = $time;
        $this->code_unique = $code_unique;
    }

    public function broadcastOn()
    {
        return new Channel("countdown.{$this->code_unique}");
    }
}
