<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class livraisonAppelOffre extends Notification
{
    use Queueable;
    private $livraison;
    /**
     * Create a new notification instance.
     */
    public function __construct($livraison)
    {
        $this->livraison = $livraison;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'idProd' => $this->livraison['idProd'],
            'quantite' => $this->livraison['quantite'],
            'id_trader' => $this->livraison['id_trader'],
            'totalSom' => $this->livraison['totalSom'],
            'localite' => $this->livraison['localite'],
            'userSender' => $this->livraison['userSender'],
            'code_livr' => $this->livraison['code_livr'],
            'prixProd' => $this->livraison['prixProd'],
            'textareaContent' => $this->livraison['textareaContent'],
            'dateTot' => $this->livraison['dateTot'],
            'dateTard' => $this->livraison['dateTard'],
        ];
    }
}
