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
            'id' => $this->reason['id'] ,
            'idProd' => $this->reason['idProd'] ?? null,
            'code_unique' => $this->reason['code_unique'] ?? null,
            'title' => $this->reason['title'],
            'description' => $this->reason['description'],
            'svg' => '<svg class="w-full text-red-700 " xmlns="http://www.w3.org/2000/svg" fill="none"                                                                                                                                                                                                                                                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>',
        ];
    }
}
