<?php

// app/Events/OldestCommentUpdated.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OldestCommentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldestCommentDate;

    public function __construct($oldestCommentDate)
    {
        $this->oldestCommentDate = $oldestCommentDate;
    }

    public function broadcastOn()
    {
        return new Channel('oldest-comment');
    }
}
