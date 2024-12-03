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
            'id' => $this->achat['id'] ?? null,
            'idProd' => $this->achat['idProd'] ?? null,
            'code_unique' => $this->achat['code_unique'] ?? null,
            'title' => $this->achat['title'],
            'description' => $this->achat['description'],
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>',
        ];
    }
}
