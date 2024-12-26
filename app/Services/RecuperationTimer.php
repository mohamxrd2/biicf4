<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Bt51\NTP\Socket;
use Bt51\NTP\Client;
use DateTime;

class RecuperationTimer
{
    private const HTTP_TIMEOUT = 2; // Réduit à 2 secondes
    private const NTP_TIMEOUT = 3;  // Timeout pour NTP
    private const SOCKET_TIMEOUT = 2; // Timeout pour les sockets

    private const NTP_SERVERS = [
        'pool.ntp.org',
        '0.pool.ntp.org',
        '1.pool.ntp.org',
    ];

    private $time;
    private $timentp;
    private $error = null;
    private $lastSuccessfulServer = null;

    protected $timeServers = [
        'https://worldtimeapi.org/api/timezone/Etc/UTC',
        'http://worldclockapi.com/api/json/utc/now',
        'https://timeapi.io/api/Time/current/zone?timeZone=UTC',
    ];

    public function getTime(): int
    {
        try {
            // Définir une limite de temps globale
            set_time_limit(10);

            $this->fetchServerTime();

            if ($this->error !== null) {
                Log::warning('RecuperationTimer: ' . $this->error);
                return $this->getFallbackTime();
            }

            return $this->time;
        } catch (Exception $e) {
            Log::error('RecuperationTimer error: ' . $e->getMessage());
            return $this->getFallbackTime();
        }
    }

    private function fetchServerTime(): void
    {
        // Essayer d'abord l'heure système si on est en mode développement
        if (app()->environment('local')) {
            $this->time = $this->getFallbackTime();
            return;
        }

        // Essayer un seul serveur HTTP aléatoire
        if ($this->tryRandomHttpServer()) {
            return;
        }

        // Essayer un seul serveur NTP aléatoire
        if ($this->tryRandomNtpServer()) {
            return;
        }

        $this->error = "Impossible de récupérer l'heure depuis les serveurs";
    }

    private function tryRandomHttpServer(): bool
    {
        $server = $this->timeServers[array_rand($this->timeServers)];

        try {
            $response = Http::timeout(self::HTTP_TIMEOUT)
                          ->connectTimeout(self::HTTP_TIMEOUT)
                          ->get($server);

            if ($response->successful()) {
                $data = $response->json();
                $this->time = $this->parseTimeFromResponse($data);
                $this->error = null;
                $this->lastSuccessfulServer = $server;
                return true;
            }
        } catch (Exception $e) {
            Log::debug("Échec HTTP {$server}: " . $e->getMessage());
        }

        return false;
    }

    private function tryRandomNtpServer(): bool
    {
        $ntpServer = self::NTP_SERVERS[array_rand(self::NTP_SERVERS)];

        try {
            // Configuration du timeout du socket
            $socket = new Socket($ntpServer, 123);
            $ntp = new Client($socket);

            // Définir un timeout plus court pour NTP
            set_time_limit(self::NTP_TIMEOUT);

            $this->timentp = $ntp->getTime();

            if ($this->timentp instanceof DateTime) {
                $timestampSeconds = $this->timentp->getTimestamp();
                $this->time = ($timestampSeconds * 1000) +
                             (int)($this->timentp->format('u') / 1000);
                $this->error = null;
                $this->lastSuccessfulServer = $ntpServer;
                return true;
            }
        } catch (Exception $e) {
            Log::debug("Échec NTP {$ntpServer}: " . $e->getMessage());
        }

        return false;
    }

    private function parseTimeFromResponse(array $data): int
    {
        $timeFields = ['datetime', 'currentDateTime', 'dateTime'];

        foreach ($timeFields as $field) {
            if (!empty($data[$field])) {
                $timestamp = strtotime($data[$field]);
                if ($timestamp !== false) {
                    return $timestamp * 1000;
                }
            }
        }

        throw new Exception('Format de réponse invalide');
    }

    private function getFallbackTime(): int
    {
        return (int) (microtime(true) * 1000);
    }

    public function getLastSuccessfulServer(): ?string
    {
        return $this->lastSuccessfulServer;
    }
}
