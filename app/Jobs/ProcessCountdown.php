<?php

namespace App\Jobs;

use App\Models\Countdown;
use App\Services\RecuperationTimer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\CountdownTick;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ProcessCountdown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $countdownId;
    public $tries = 1;
    public $timeout = 30;
    public $time;
    public $error;
    public $timestamp;
    public $code_unique;

    protected $recuperationTimer;

    public function __construct($countdownId, $code_unique)
    {
        $this->countdownId = $countdownId;
        $this->code_unique = $code_unique;
        $this->onQueue('default');
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function handle()
    {
        $this->timeServer();
        $countdown = Countdown::find($this->countdownId);

        if (!$countdown || !$countdown->is_active || !$countdown->end_time) {
            return;
        }

        $lockKey = "countdown_lock_{$this->code_unique}";

        if (Cache::add($lockKey, true, 1)) {
            try {
                $timeRemaining = $countdown->end_time->diffInSeconds($this->timestamp);

                if ($timeRemaining <= 1) {
                    $countdown->update([
                        'is_active' => false,
                        'time_remaining' => 0
                    ]);
                    broadcast(new CountdownTick($timeRemaining, $this->code_unique));
                    return;
                }

                $countdown->update(['time_remaining' => $timeRemaining]);
                broadcast(new CountdownTick($timeRemaining, $this->code_unique));

                if ($timeRemaining > 1) {
                    dispatch(new self($this->countdownId, $this->code_unique))
                        ->onConnection('database')
                        ->delay(now()->addSeconds(2));
                }
            } finally {
                Cache::forget($lockKey);
            }
        }
    }

    public function timeServer()
    {
        // Faire plusieurs tentatives de récupération pour plus de précision
        $attempts = 3;
        $times = [];

        for ($i = 0; $i < $attempts; $i++) {
            // Récupération de l'heure via le service
            $currentTime = $this->recuperationTimer->getTime();
            if ($currentTime) {
                $times[] = $currentTime;
            }
            // Petit délai entre chaque tentative
            usleep(50000); // 50ms
        }

        if (empty($times)) {
            // Si aucune tentative n'a réussi, utiliser l'heure système
            $this->error = "Impossible de synchroniser l'heure. Utilisation de l'heure système.";
            $this->time = now()->timestamp * 1000;
        } else {
            // Utiliser la médiane des temps récupérés pour plus de précision
            sort($times);
            $medianIndex = floor(count($times) / 2);
            $this->time = $times[$medianIndex];
            $this->error = null;
        }

        // Convertir en secondes
        $seconds = intval($this->time / 1000);
        // Créer un objet Carbon pour le timestamp
        $this->timestamp = Carbon::createFromTimestamp($seconds);
    }

    public function failed(\Throwable $exception)
    {
        Cache::forget("countdown_lock_{$this->code_unique}");

        $countdown = Countdown::find($this->countdownId);
        if ($countdown) {
            $countdown->update([
                'is_active' => false,
                'time_remaining' => 0
            ]);
            broadcast(new CountdownTick(0, $this->code_unique));
        }
    }
}
