<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RetraitCode extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $retrait;
    public function __construct($retrait)
    {
        $this->retrait = $retrait;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->retrait['title'],
            'description' => $this->retrait['description'],
            'svg' => $this->retrait['svg'],
            'code_unique' => $this->retrait['code_unique'],
            'psap' => $this->retrait['psap'],
            'userId' => $this->retrait['userId'],
            'amount' => $this->retrait['amount'],
            'codeRetrait' => $this->retrait['codeRetrait'] ?? null,
       
            

        ];
    }

}
