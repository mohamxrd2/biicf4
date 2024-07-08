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
        $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(1))
            ->with('sender') // Charger la relation userSender
            ->get();

        foreach ($countdowns as $countdown) {
            Notification::send($countdown->sender, new CountdownNotification());

            $countdown->update(['notified' => true]);
        }
    }
}
