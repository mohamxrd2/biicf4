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

    public $credit;
    public $portionCapital;
    public $portionInteret;
    public $message;

    public function __construct($credit, $portionCapital, $portionInteret, $message = null)
    {
        $this->credit = $credit;
        $this->portionCapital = $portionCapital;
        $this->portionInteret = $portionInteret;
        $this->message = $message;
    }
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'credit_id' => $this->credit->id,
            'montant_total' => $this->credit->montant_total,
            'portion_capital' => $this->portionCapital,
            'portion_interet' => $this->portionInteret,
            'date_portion' => Carbon::today()->toDateString(),
            'message' => $this->message,
        ];
    }
}
