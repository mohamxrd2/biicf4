<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class livraisonVerif extends Notification
{
    use Queueable;
    private $livraisonVerif;
    /**
     * Create a new notification instance.
     */
    public function __construct($livraisonVerif)
    {
        $this->livraisonVerif = $livraisonVerif;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'id_prod' => $this->livraisonVerif['id_prod'],
            'quantite' => $this->livraisonVerif['quantite'],
            'id_trader' => $this->livraisonVerif['id_trader'],
            'totalSom' => $this->livraisonVerif['totalSom'],
            'localite' => $this->livraisonVerif['localite'],
            'userSender' => $this->livraisonVerif['userSender'],
            'code_livr' => $this->livraisonVerif['code_livr']
        ];
    }
}
