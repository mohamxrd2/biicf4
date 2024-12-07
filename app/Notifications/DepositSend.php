<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositSend extends Notification implements ShouldQueue
{
    use Queueable;

    private $deposit;

    /**
     * Create a new notification instance.
     */
    public function __construct($deposit)
    {
        $this->deposit = $deposit;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->deposit['title'],
            'description' => $this->deposit['description'],
            'code_unique' => $this->deposit['code_unique'],
            'svg' => $this->deposit['svg'],
            'user_id' => $this->deposit['user_id'],
            'amount' => $this->deposit['amount'],
            'roi' => $this->deposit['roi'],
            'receipt' => $this->deposit['receipt'],
        ];
    }
}