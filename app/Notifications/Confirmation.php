<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Confirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $achat;

    public function __construct($achat)
    {
        $this->achat = $achat;
    }

    /**
     * Canaux de notification.
     */
    public function via($notifiable)
    {
        return ['database']; // Ajouter 'mail' si vous souhaitez aussi envoyer un email
    }

    /**
     * Notification via Base de données.
     */
    public function toDatabase($notifiable)
    {
        return [
            'achat_id' => $this->achat['idAchat'] ?? null,
            'id_appeloffre' => $this->achat['id_appeloffre'] ?? null,
            'idProd' => $this->achat['idProd'] ?? null,
            'code_unique' => $this->achat['code_unique'] ?? null,
            'title' => $this->achat['title'],
            'description' => $this->achat['description'],
        ];
    }
}
