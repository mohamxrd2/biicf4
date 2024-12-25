<?php

namespace App\Jobs;

use App\Models\Countdown;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CountdownTick;
use Illuminate\Support\Facades\Cache;

class ProcessCountdown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $countdownId;
    public $tries = 1;
    public $timeout = 30;

    public function __construct($countdownId)
    {
        $this->countdownId = $countdownId;

    }

    public function handle()
    {
        $countdown = Countdown::find($this->countdownId);

        if (!$countdown || !$countdown->is_active || !$countdown->end_time) {
            return;
        }

        $lockKey = "countdown_lock_{$this->countdownId}";

        // Vérifier si un autre processus est déjà en cours
        if (Cache::has($lockKey)) {
            return;
        }

        // Acquérir le verrou
        Cache::put($lockKey, true, 2);

        try {
            $timeRemaining = $countdown->end_time->diffInSeconds(now());

            if ($timeRemaining <= 0) {
                $countdown->update([
                    'is_active' => false,
                    'time_remaining' => 0
                ]);
                broadcast(new CountdownTick(0));
                return;
            }

            // Mettre à jour le temps restant
            $countdown->update(['time_remaining' => $timeRemaining]);
            broadcast(new CountdownTick($timeRemaining));

            // Programmer le prochain tick
            if ($timeRemaining > 1) {
                dispatch(new self($this->countdownId))->delay(now()->addSecond());
            }
        } finally {
            Cache::forget($lockKey);
        }
    }

    public function failed(\Throwable $exception)
    {
        Cache::forget("countdown_lock_{$this->countdownId}");

        $countdown = Countdown::find($this->countdownId);
        if ($countdown) {
            $countdown->update([
                'is_active' => false,
                'time_remaining' => 0
            ]);
            broadcast(new CountdownTick(0));
        }
    }
}
