<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Retrait extends Notification implements ShouldQueue
{
    use Queueable;

    private $retrait;

    /**
     * Create a new notification instance.
     */
    public function __construct($retrait)
    {
        $this->retrait = $retrait;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'psap' => $this->retrait['psap'],
            'userId' => $this->retrait['userId'],
            'amount' => $this->retrait['amount'],
            

        ];
    }

}
