<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppelOffreTerminer extends Notification
{
    use Queueable;
    private $negos;
    /**
     * Create a new notification instance.
     */
    public function __construct($negos)
    {
        $this->negos = $negos;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Negociation terminé !',
            'prix_trade' => $this->negos['prix_trade'],
            'id_trader' => $this->negos['id_trader'],
            'name' => $this->negos['name'],
            'quantite' => $this->negos['quantite']

        ];
    }
}
