<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppelOffreGrouperNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $achat;

    public function __construct($achat)
    {
        $this->achat = $achat;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'id_appelGrouper' => $this->achat['id_appelGrouper'] ?? null,
            'totalPersonnes' => $this->achat['totalPersonnes'] ?? null,
            'code_unique' => $this->achat['code_unique'],
            'title' => $this->achat['title'] ?? null,
            'description' => $this->achat['description'] ?? null,
        ];
    }
}
