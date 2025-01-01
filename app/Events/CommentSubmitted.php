<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $prix;
    public $commentId;
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($prix, $commentId)
    {
        $this->prix = $prix;
        $this->commentId = $commentId;
        $this->timestamp = now()->toIso8601String(); // Ajoute l'heure actuelle

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('comments');
    }

    public function broadcastWith()
    {
        return [
            'prix' => $this->prix,
            'commentId' => $this->commentId,

        ];
    }
}
