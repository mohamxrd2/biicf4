<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppelOffreTerminer extends Notification implements ShouldQueue
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
            'idProd' => $this->details['idProd'],
            'quantiteC' => $this->details['quantiteC'],
        ];
    }
}
