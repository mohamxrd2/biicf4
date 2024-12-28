<?php

namespace App\Console\Commands;

use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\userquantites;
use App\Notifications\OffreNegosDone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AjoutQoffre extends Command
{
    protected $signature = 'app:ajout-qoffre';
    protected $description = 'Check if the time is finished to submit a notification to consumption user';

    public function handle()
    {
        try {
            // Récupérer les countdowns non notifiés qui ont dépassé 2 minutes
            $countdowns = Countdown::where('notified', false)
                ->where('start_time', '<=', now()->subMinutes(2))
                ->where('difference', 'offreGrouper')
                ->with(['sender', 'achat', 'appelOffre', 'appelOffreGrouper'])
                ->get();

            if ($countdowns->isEmpty()) {
                Log::info('Aucun countdown à traiter.');
                return;
            }

            Log::info('Traitement de ' . $countdowns->count() . ' countdowns.');

            // Récupérer les offres groupées associées
            $codeUniques = $countdowns->pluck('code_unique')->unique();
            $offreGroupes = OffreGroupe::whereIn('code_unique', $codeUniques)
                ->with(['user', 'produit'])
                ->get();

            foreach ($offreGroupes as $offre) {
                DB::beginTransaction();
                try {
                    $this->processOffre($offre, $countdowns);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Erreur lors du traitement de l\'offre ' . $offre->code_unique, [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur générale dans AjoutQoffre', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function processOffre(OffreGroupe $offre, $countdowns)
    {
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

        Log::info('Traitement terminé pour l\'offre: ' . $offre->code_unique, [
            'quantites' => $quantitesParUser,
            'user_id' => $offre->user->id
        ]);
    }

    protected function getUserQuantities(string $codeUnique): array
    {
        return userquantites::where('code_unique', $codeUnique)
            ->get()
            ->groupBy('user_id')
            ->map(fn($group) => $group->sum('quantite'))
            ->toArray();
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
