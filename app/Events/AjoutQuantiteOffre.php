<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AjoutQuantiteOffre
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quantite;

    public function __construct($quantite)
    {
        $this->quantite = $quantite;
    }

    public function broadcastOn()
    {
        return new Channel('quantite-channel');
    }

    public function broadcastAs()
    {
        return 'ajout-quantite-offre';
    }
}
