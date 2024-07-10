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
            'sender_name' => $this->details['sender_name'],
            'code_unique' => $this->details['code_unique'],
            'prixTrade' => $this->details['prixTrade'],
            'id_trader' => $this->details['id_trader'],
            'id_prod' => $this->details['id_prod'],
            'quantiteC' => $this->details['quantiteC'],
        ];
    }
}
