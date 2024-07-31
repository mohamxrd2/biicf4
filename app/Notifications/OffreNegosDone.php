<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OffreNegosDone extends Notification
{
    use Queueable;

    protected $offredone;

    /**
     * Create a new notification instance.
     *
     * @param array $offredone
     * @return void
     */
    public function __construct(array $offredone)
    {
        $this->offredone = $offredone;
    }

    /**
     * Determine the channels the notification will be sent on.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        Log::info('Notification data: ', $this->offredone); // Log the data being sent

        return [
            'quantite' => $this->offredone['quantite'],
            'produit_id' => $this->offredone['produit_id'],
            'produit_name' => $this->offredone['produit_name'],
            'code_unique' => $this->offredone['code_unique'],
        ];
    }
}
