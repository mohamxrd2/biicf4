<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchatBiicf extends Notification implements ShouldQueue
{
    use Queueable;

    private $achat;

    /**
     * Create a new notification instance.
     */
    public function __construct($achat)
    {
        $this->achat = $achat;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [

            'nameProd' => $this->achat['nameProd'],
            'idAchat' => $this->achat['idAchat'],
            'idProd' => $this->achat['idProd'],
            'code_unique' => $this->achat['code_unique'] ?? null,
            'title' => $this->achat['title'] ?? null,
            'description' => $this->achat['description'] ?? null,
        ];
    }

}
