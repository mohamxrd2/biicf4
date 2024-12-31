<?php

namespace App\Console\Commands;

use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\userquantites;
use App\Notifications\OffreNegosDone;
use Illuminate\Console\Command;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class AjoutQoffre extends Command
{
    protected $signature = 'app:ajout-qoffre';
    protected $description = 'Check if the time is finished to submit a notification to consumption user';
    protected $recuperationTimer;
    public $time;
    public $error;
    public $timestamp;
    public $countdownId;
    public $isRunning;
    public $timeRemaining;

    public function __construct()
    {
        parent::__construct();
        $this->recuperationTimer = new RecuperationTimer();
    }
    public function handle()
    {
        try {

            $timeSync = new TimeSyncService($this->recuperationTimer);
            $result = $timeSync->getSynchronizedTime();
            $serverTime = $result['timestamp']->subMinutes(2);

            $countdowns = Countdown::where('notified', false)
                ->where('end_time', '<=', $serverTime)
                ->where('difference', 'offreGrouper')
                ->with(['sender', 'achat', 'appelOffre', 'appelOffreGrouper'])
                ->get();

            if ($countdowns->isEmpty()) {
                Log::info('Aucun countdown à traiter.');
                return;
            }

            DB::beginTransaction();

            $codeUniques = $countdowns->pluck('code_unique')->unique();
            $offreGroupes = OffreGroupe::whereIn('code_unique', $codeUniques)
                ->with(['user', 'produit'])
                ->get();

            foreach ($offreGroupes as $offre) {
                $this->processOffre($offre, $countdowns);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur générale dans AjoutQoffre', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function processOffre($offre, $countdowns)
    {
        // Actualiser le timer avant de commencer
        $this->timeServer();
        // Vérifier si l'offre a un utilisateur associé
        if (!$offre->user) {
            Log::error('Utilisateur non trouvé pour l\'offre: ' . $offre->code_unique);
            return;
        }

        // Récupérer les quantités par utilisateur
        $quantitesParUser = $this->getUserQuantities($offre->code_unique);
        if (empty($quantitesParUser)) {
            Log::warning('Aucune quantité trouvée pour le code unique: ' . $offre->code_unique);
            return;
        }

        // Envoyer la notification
        $this->sendNotification($offre, $quantitesParUser);

        // Marquer les countdowns comme notifiés
        $this->markCountdownsAsNotified($countdowns, $offre->code_unique);
    }

    protected function getUserQuantities($codeUnique)
    {
        return userquantites::where('code_unique', $codeUnique)
            ->get()
            ->groupBy('user_id')
            ->map(fn($group) => $group->sum('quantite'))
            ->toArray();
    }
    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();

        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }
    protected function sendNotification(OffreGroupe $offre, array $quantitesParUser): void
    {
        $notificationData = [
            'quantite_totale' => array_sum($quantitesParUser),
            'details_par_user' => $quantitesParUser,
            'idProd' => $offre->produit_id,
            'id_sender' => $offre->user->id,
            'code_unique' => $offre->code_unique,
        ];

        Notification::send($offre->user, new OffreNegosDone($notificationData));
        $offre->update(['notified' => true]);
    }

    protected function markCountdownsAsNotified($countdowns, string $codeUnique): void
    {
        $countdowns->where('code_unique', $codeUnique)
            ->each(function ($countdown) {
                $countdown->update(['notified' => true]);
            });
    }
}
