<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Notifications\CountdownNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckCountdowns extends Command
{
    protected $signature = 'check:countdowns';
    protected $description = 'Check countdowns and send notifications if time is up';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Récupérer les countdowns non notifiés et dont le start_time est passé depuis au moins une minute
        $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(1))
            ->with('sender') // Charger la relation userSender
            ->get();

        foreach ($countdowns as $countdown) {
            // Envoyer la notification à l'utilisateur expéditeur
            Notification::send($countdown->sender, new CountdownNotification());

            // Mettre à jour le statut notified à true
            $countdown->update(['notified' => true]);
        }
    }
}
