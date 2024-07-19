<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CountdownNotification extends Notification
{
    use Queueable;
    public $details;
    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'sender_name' => $this->details['sender_name'] ?? null,
            'code_unique' => $this->details['code_unique'] ?? null,
            'prixTrade' => $this->details['prixTrade'] ?? null,
            'id_trader' => $this->details['id_trader'] ?? null,
            'idProd' => $this->details['idProd'] ?? null,
            'quantiteC' => $this->details['quantiteC'] ?? null,
        ];
    }
}
