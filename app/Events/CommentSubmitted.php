<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $code_unique;
    public $comment;

    public function __construct($code_unique, $comment = null)
    {
        $this->code_unique = $code_unique;
        $this->comment = $comment;
    }

    public function broadcastOn()
    {
        return new Channel("comments.{$this->code_unique}");
    }

    public function broadcastWith()
    {
        return [
            'code_unique' => $this->code_unique,
            'comment' => $this->comment ? $this->comment->toArray() : null,
            'timestamp' => now()->toIso8601String()
        ];
    }
}
