<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DepositClientNotification extends Notification
{
    use Queueable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // ou d'autres canaux selon vos besoins
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau dépôt client')
            ->line('Un nouveau dépôt a été soumis.')
            ->line('Montant : ' . $this->data['montant'])
            ->action('Voir le reçu', url('/'))
            ->line('Merci pour votre confiance!');
    }

    public function toArray($notifiable)
    {
        return [
            'montant' => $this->data['montant'],
            'recu' => $this->data['recu'],
            'user_id' => $this->data['user_id'],
        ];
    }
}
