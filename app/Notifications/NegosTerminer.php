<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NegosTerminer extends Notification
{
    use Queueable;
    private $offre;
    /**
     * Create a new notification instance.
     */
    public function __construct($offre)
    {
        $this->offre = $offre;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'code_unique' => $this->offre['code_unique'] ?? null,
            'prixProd' => $this->offre['prixProd'] ?? null,
            'idProd' => $this->offre['idProd'] ?? null,
            'id_trader' => $this->offre['id_trader'] ?? null,
            'id_sender' => $this->offre['id_sender'] ?? null,
        ];
    }
}
