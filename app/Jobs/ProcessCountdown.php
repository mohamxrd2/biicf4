<?php

namespace App\Jobs;

use App\Models\Countdown;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CountdownTick;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class ProcessCountdown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $countdownId;
    public $code_unique;
    public $tries = 3;
    public $timeout = 120;
    public $time;
    public $error;
    public $timestamp;

    protected $recuperationTimer;

    public function __construct($countdownId, $code_unique)
    {
        $this->countdownId = $countdownId;
        $this->code_unique = $code_unique;
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function handle()
    {
        $this->timeServer();

        $countdown = Countdown::where('id', $this->countdownId)
            ->where('code_unique', $this->code_unique)
            ->where('is_active', true)
            ->where('notified', false)
            ->first();

        if (!$countdown || !$countdown->end_time) {
            return;
        }

        $lockKey = "countdown_lock_{$this->code_unique}_{$this->countdownId}";

        if (Cache::add($lockKey, true, 1)) {
            try {
                if ($countdown->end_time->isPast()) {
                    $this->finalizeCountdown($countdown);
                    return;
                }

                // Calcul plus précis du temps restant
                $timeRemaining = $countdown->end_time->diffInSeconds($this->timestamp);

                // Arrondir à la seconde la plus proche
                $timeRemaining = round($timeRemaining);

                if ($timeRemaining <= 0) {
                    $this->finalizeCountdown($countdown);
                    return;
                }

                // Mettre à jour et broadcaster
                $countdown->update(['time_remaining' => $timeRemaining]);
                broadcast(new CountdownTick($timeRemaining, $this->code_unique));

                // Calculer le délai exact pour la prochaine seconde
                $nextTick = $this->timestamp->copy()->addSeconds(1);
                $delay = $nextTick->diffInMilliseconds($this->timestamp) / 1000;

                // Programmer la prochaine vérification avec un délai précis
                dispatch(new static($this->countdownId, $this->code_unique))
                    ->onQueue('default')
                    ->delay(now()->addSeconds($delay));

            } finally {
                Cache::forget($lockKey);
            }
        }
    }

    private function finalizeCountdown(Countdown $countdown)
    {
        if ($countdown->is_active) {
            $countdown->update([
                'is_active' => false,
                'time_remaining' => 0,
                'notified' => false
            ]);

            broadcast(new CountdownTick(0, $this->code_unique));

            // Exécuter la commande CheckCountdowns
            // Artisan::call('check:countdowns');
        }
    }

    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();
        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }

    public function failed(\Throwable $exception)
    {
        $lockKey = "countdown_lock_{$this->code_unique}_{$this->countdownId}";
        Cache::forget($lockKey);

        $countdown = Countdown::where('id', $this->countdownId)
            ->where('code_unique', $this->code_unique)
            ->first();

        if ($countdown) {
            $countdown->update([
                'is_active' => false,
                'time_remaining' => 0,
                'notified' => true
            ]);
            broadcast(new CountdownTick(0, $this->code_unique));
        }
    }
}
