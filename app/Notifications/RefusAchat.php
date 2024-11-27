<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefusAchat extends Notification
{
    use Queueable;
    private $reason;
    /**
     * Create a new notification instance.
     */
    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'achat_id' => $this->reason['idAchat'],
            'idProd' => $this->reason['idProd'] ?? null,
            'code_unique' => $this->reason['code_unique'] ?? null,
            'title' => $this->reason['title'],
            'description' => $this->reason['description'],
        ];
    }
}
