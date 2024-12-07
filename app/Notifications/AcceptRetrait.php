<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcceptRetrait extends Notification implements ShouldQueue
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
            'title' => 'Retrait accepté',
            'description' => 'Votre demande de retrait de ' . $this->retrait['amount']. ' FCFA a été acceptéé.',
            'svg' => 'retrait',
            'code_unique' => $this->retrait['code_unique'],
            'psap' => $this->retrait['psap'],
            'message' => 'Votre demande de retrait a été acceptéé',
            // You can add more details if needed

        ];
    }

}
