<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositRecu extends Notification implements ShouldQueue
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
            'user_id' => $this->deposit['user_id'],
            'amount' => $this->deposit['amount'],
            'roi' => $this->deposit['roi'],
            'id_sos' => $this->deposit['id_sos'],
            'phonenumber' => $this->deposit['phonenumber'],
            'operator' => $this->deposit['operator'],
        ];
    }
}