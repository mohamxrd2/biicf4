<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchatBiicf extends Notification implements ShouldQueue
{
    use Queueable;

    private $achat;

    /**
     * Create a new notification instance.
     */
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

            'nameProd' => $this->achat['nameProd'],
            'achat_id' => $this->achat['idAchat'],
            'idProd' => $this->achat['idProd'],
            'code_unique' => $this->achat['code_unique'] ?? null,
            'title' => $this->achat['title'] ?? null,
            'description' => $this->achat['description'] ?? null,
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6  text-green-600">
        <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                                            </svg>',

        ];
    }
}
