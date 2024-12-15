<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AjoutQuantiteOffre implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $quantite;
    public $codeUnique;

    public function __construct($quantite, $codeUnique)
    {
        $this->quantite = $quantite;
        $this->codeUnique = $codeUnique;
    }

    public function broadcastOn()
    {
        return new Channel('quantite-channel');
    }

    public function broadcastWith()
    {
        return [
            'quantite' => $this->quantite,
            'codeUnique' => $this->codeUnique,
        ];
    }
}
