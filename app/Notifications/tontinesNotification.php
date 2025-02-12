<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class tontinesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payement;

    public function __construct($payement)
    {
        $this->payement = $payement;
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

            'code_unique' => $this->payement['code_unique'] ?? null,
            'title' => $this->payement['title'],
            'description' => $this->payement['description'],
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>',
        ];
    }
}
