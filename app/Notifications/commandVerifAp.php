<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class commandVerifAp extends Notification
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
            'fournisseur' => $this->accept['fournisseur'],
            'quantite' => $this->accept['quantite'],
            'localité' => $this->accept['localité'],
            'livreur' => $this->accept['livreur'],
            'prixTrade' => $this->accept['prixTrade'],
            'prixProd' => $this->accept['prixProd'],
            'date_tot' => $this->accept['date_tot'],
            'date_tard' => $this->accept['date_tard'],
        ];
    }
}
