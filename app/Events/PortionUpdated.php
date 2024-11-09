<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PortionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $creditId;
    public $emprunteurId;
    public $montantRestant;

    public function __construct($creditId, $emprunteurId, $montantRestant)
    {
        $this->creditId = $creditId;
        $this->emprunteurId = $emprunteurId;
        $this->montantRestant = $montantRestant;
    }

    public function broadcastOn()
    {
        return new Channel('portions-journalieres');
    }

    public function broadcastWith()
    {
        return [
            'creditId' => $this->creditId,
            'emprunteurId' => $this->emprunteurId,
            'montantRestant' => $this->montantRestant, // Ajoute l'heure actuelle

        ];
    }
}
