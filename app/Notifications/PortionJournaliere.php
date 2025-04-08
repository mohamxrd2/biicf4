<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortionJournaliere extends Notification implements ShouldQueue
{
    use Queueable;

    public $echec;
    public $credit;

    public $message;

    public function __construct($echec,  $message = null, $credit)
    {
        $this->echec = $echec;
        $this->message = $message;
        $this->credit = $credit;
    }
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->echec,
            'description' => $this->message,
            'orderId' => $this->credit->id,
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>',
        ];
    }
}
