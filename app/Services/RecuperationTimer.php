<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Bt51\NTP\Socket;
use Bt51\NTP\Client;
use DateTime;
use Illuminate\Support\Facades\Log;

class RecuperationTimer
{
    public $time;
    public $timentp;
    public $error = null;
    private $lastTime = null;

    protected $timeServers = [
        'https://worldtimeapi.org/api/timezone/Etc/UTC',
        'http://worldclockapi.com/api/json/utc/now',
        'https://timeapi.io/api/Time/current/zone?timeZone=UTC',
    ];

    public function getTime()
    {
        try {
            // Récupérer le dernier temps connu depuis le cache
            $this->lastTime = Cache::get('last_server_time');

            // Obtenir le nouveau temps
            $newTime = $this->fetchCurrentTime();

            // Si on a un temps précédent, vérifier la cohérence
            if ($this->lastTime) {
                // Si le nouveau temps est inférieur au dernier temps connu
                if ($newTime < $this->lastTime) {
                    // Utiliser le dernier temps connu + un petit incrément
                    $newTime = $this->lastTime + 1000; // Ajouter 1 seconde
                }
            }

            // Mettre à jour le cache avec le nouveau temps
            Cache::put('last_server_time', $newTime, now()->addMinutes(60));

            $this->time = $newTime;
            return $this->time;

        } catch (Exception $e) {
            // En cas d'erreur, utiliser le dernier temps connu ou l'heure système
            $this->error = "Erreur de synchronisation : {$e->getMessage()}";
            return $this->lastTime ?? (now()->timestamp * 1000);
        }
    }

    private function fetchCurrentTime()
    {
        // Essayer d'abord les serveurs de temps externes
        try {
            foreach ($this->timeServers as $server) {
                $response = Http::timeout(5)->get($server);
                if ($response->successful()) {
                    return $this->parseTimeFromResponse($response->json());
                }
            }
        } catch (Exception $e) {
            // Logger l'erreur mais continuer avec NTP
            Log::warning("Erreur avec les serveurs de temps externes: " . $e->getMessage());
        }

        // Si les serveurs externes échouent, essayer NTP
        try {
            $socket = new Socket('0.pool.ntp.org', 123);
            $ntp = new Client($socket);
            $this->timentp = $ntp->getTime();

            if ($this->timentp instanceof DateTime) {
                $timestampSeconds = $this->timentp->getTimestamp();
                return $timestampSeconds * 1000 + (int)($this->timentp->format('u') / 1000);
            }
        } catch (Exception $e) {
            // Logger l'erreur mais continuer avec l'heure système
            Log::warning("Erreur avec NTP: " . $e->getMessage());
        }

        // En dernier recours, utiliser l'heure système
        return now()->timestamp * 1000;
    }

    private function parseTimeFromResponse($data)
    {
        if (isset($data['datetime'])) {
            return strtotime($data['datetime']) * 1000;
        } elseif (isset($data['currentDateTime'])) {
            return strtotime($data['currentDateTime']) * 1000;
        } elseif (isset($data['dateTime'])) {
            return strtotime($data['dateTime']) * 1000;
        }

        throw new Exception('Format de réponse invalide.');
    }
}
