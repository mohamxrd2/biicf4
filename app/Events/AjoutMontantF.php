<?php

// app/Events/DebutDeNegociation.php
namespace App\Events;

use App\Models\Projet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AjoutMontantF implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $projet;

    public function __construct($projet)
    {
        $this->projet = $projet;
    }

    public function broadcastOn()
    {
        return new Channel('ajout-montant');
    }
}
