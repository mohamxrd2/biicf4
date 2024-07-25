<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class commandVerif extends Notification
{
    use Queueable;
    private $accept;
    /**
     * Create a new notification instance.
     */
    public function __construct($accept)
    {
        $this->accept = $accept;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'idProd' => $this->accept['idProd'],
            'code_unique' =>$this->accept['code_unique'],
            'id_trader' => $this->accept['id_trader'],
            'quantite' => $this->accept['quantite'],
            'localité' => $this->accept['localité'],
            'id_livreur' => $this->accept['id_livreur'],
            'prixTrade' => $this->accept['prixTrade'],
            'prixProd' => $this->accept['prixProd']
        ];
    }
}
