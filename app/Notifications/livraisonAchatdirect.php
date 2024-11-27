<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class livraisonAchatdirect extends Notification
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
            'code_livr' => $this->livraison['code_livr'],
            'textareaContent' => $this->livraison['textareaContent'],
            'photoProd' => $this->livraison['photoProd'],
            'achat_id' => $this->livraison['idAchat'],

        ];
    }
}
