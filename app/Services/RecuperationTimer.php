<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Bt51\NTP\Socket;
use Bt51\NTP\Client;

class RecuperationTimer
{
    public $time;
    public $timentp;
    public $error = null; // Message d'erreur en cas de problème

    protected $timeServers = [
        'https://worldtimeapi.org/api/timezone/Etc/UTC',
        'http://worldclockapi.com/api/json/utc/now',
        'https://timeapi.io/api/Time/current/zone?timeZone=UTC',
    ];

    // Méthode principale pour obtenir l'heure
    public function getTime()
    {
        try {
            // En cas d'échec, utiliser l'heure du serveur comme secours
            $this->fetchServerTime();
            
        } catch (Exception $e) {
            // Initialisation de la connexion au serveur NTP
            $socket = new Socket('0.pool.ntp.org', 123);
            $ntp = new Client($socket);

            // Récupération de l'heure depuis le serveur NTP
            $this->timentp = $ntp->getTime();
            $this->time = Carbon::parse($this->timentp)->toIso8601String();
        }

        return $this->time;
    }

    // Méthode pour récupérer l'heure depuis différents serveurs
    private function fetchServerTime()
    {
        foreach ($this->timeServers as $server) {
            try {
                $response = Http::timeout(5)->get($server);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->time = $this->parseTimeFromResponse($data);
                    $this->error = null; // Réinitialise l'erreur
                    return;
                }
            } catch (Exception $e) {
                $this->error = "Erreur avec le serveur : {$e->getMessage()}";
                continue;
            }
        }
    }

    // Méthode pour parser la réponse des serveurs
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
