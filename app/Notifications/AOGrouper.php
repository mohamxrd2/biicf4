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
            'title' => 'Groupage Clients',
            'description' => 'Une nouvelle offre groupée est disponible.Cliquez pour participer',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
          </svg>
          ',
        ];
    }
}
