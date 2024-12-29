<?php

namespace App\Services\TimeSync;

use Carbon\Carbon;

class TimeSyncService
{
    private $timeProvider;
    private $attempts;
    private $delayMicroseconds;

    public function __construct($timeProvider, $attempts = 3, $delayMicroseconds = 50000)
    {
        $this->timeProvider = $timeProvider;
        $this->attempts = $attempts;
        $this->delayMicroseconds = $delayMicroseconds;
    }

    public function getSynchronizedTime()
    {
        $times = $this->collectTimeSamples();

        if (empty($times)) {
            return [
                'time' => now()->timestamp * 1000,
                'error' => "Impossible de synchroniser l'heure. Utilisation de l'heure système.",
                'timestamp' => now()
            ];
        }

        sort($times);
        $medianIndex = floor(count($times) / 2);
        $time = $times[$medianIndex];

        return [
            'time' => $time,
            'error' => null,
            'timestamp' => Carbon::createFromTimestamp(intval($time / 1000))
        ];
    }

    private function collectTimeSamples()
    {
        $times = [];
        for ($i = 0; $i < $this->attempts; $i++) {
            $currentTime = $this->timeProvider->getTime();
            if ($currentTime) {
                $times[] = $currentTime;
            }
            usleep($this->delayMicroseconds);
        }
        return $times;
    }
}
