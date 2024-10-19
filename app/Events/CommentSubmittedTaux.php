<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSubmittedTaux implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $taux;
    public $commentId;
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($taux, $commentId)
    {
        $this->taux = $taux;
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
            'taux' => $this->taux,
            'commentId' => $this->commentId,
            'timestamp' => $this->timestamp = now()->toIso8601String(), // Ajoute l'heure actuelle

        ];
    }
}
