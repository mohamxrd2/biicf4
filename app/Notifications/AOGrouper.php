<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AOGrouper extends Notification implements ShouldQueue
{
    use Queueable;

    private $codeunique;
    private $offreId; // Utiliser un nom plus explicite comme $offreId

    /**
     * Create a new notification instance.
     *
     * @param string $codeunique
     * @param int $offreId
     */
    public function __construct($codeunique, $offreId)
    {
        $this->codeunique = $codeunique;
        $this->offreId = $offreId;
    }

    /**
     * Determine the channels the notification will broadcast on.
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
        return [
            'offre_id' => $this->offreId,
            'code_unique' => $this->codeunique,
            'message' => 'Une nouvelle offre groupée est disponible.',
        ];
    }
}
