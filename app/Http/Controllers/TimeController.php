<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bt51\NTP\Socket;
use Bt51\NTP\Client;
use Carbon\Carbon;

class TimeController extends Controller
{
    public function getServerTime()
    {
        try {
            // Initialisation de la connexion au serveur NTP
            $socket = new Socket('0.pool.ntp.org', 123);
            $ntp = new Client($socket);

            // Récupération de l'heure depuis le serveur NTP
            $time = $ntp->getTime();

            // Convertir en format ISO 8601
            $serverTime = Carbon::parse($time)->toIso8601String();

            // Retourner l'heure sous forme de réponse JSON
            return response()->json(['serverTime' => $serverTime]);
        } catch (\Exception $e) {
            // En cas d'erreur, on renvoie une erreur avec un message
            return response()->json(['error' => 'Failed to sync with NTP server', 'message' => $e->getMessage()], 500);
        }
    }
}
