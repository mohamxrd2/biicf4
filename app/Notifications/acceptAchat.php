<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class acceptAchat extends Notification
{
    use Queueable;
    private $accept;
    /**
     * Create a new notification instance.
     */
    public function __construct($accept)
    {
        $this->accept = $accept;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Votre achat a été accepté.',
            'accept' => $this->accept,
        ];
    }
}
