<?php

namespace App\Console\Commands;

use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\ProduitService;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\OffreNegosDone;
use App\Services\RecuperationTimer;
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
        $countdowns = $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(2))
            ->where('difference', 'offreGrouper')
            ->with(['sender', 'achat', 'appelOffre', 'appelOffreGrouper'])
            ->get();

        $codeUniques = $countdowns->pluck('code_unique')->unique();

        $OffreGroupes = OffreGroupe::whereIn('code_unique', $codeUniques)->get();

        if ($countdowns->isEmpty()) {
            Log::info('No countdowns found to process.');
            return;
        }

        Log::info('Found ' . $countdowns->count() . ' countdowns to process.');

        foreach ($OffreGroupes as $offre) {
            $this->processCountdown($offre, $countdowns);
        }
    }

    protected function processCountdown($offre, $countdowns)
    {

        $codeUnique = $offre->code_unique;
        $quantitesParUser = $this->getUserQuantities($codeUnique);

        Log::info('Quantities per user for unique code ' . $codeUnique . ': ' . json_encode($quantitesParUser));

        $sender = $offre->user;

        if (!$sender) {
            Log::error('Sender not found for countdown: ' . $codeUnique);
            return;
        }

        $this->sendNotification($offre, $sender, $quantitesParUser);
        // Marquer les Countdown liés comme notifiés
        $this->markCountdownsAsNotified($countdowns, $codeUnique);
    }
    private function markCountdownsAsNotified($countdowns, $codeUnique)
    {
        $countdownsToUpdate = $countdowns->where('code_unique', $codeUnique);
        foreach ($countdownsToUpdate as $countdown) {
            $countdown->update(['notified' => true]);
        }

        Log::info('Countdowns marqués comme notifiés:', $countdownsToUpdate->toArray());
    }
    protected function getUserQuantities($codeUnique)
    {
        return userquantites::where('code_unique', $codeUnique)
            ->get()
            ->groupBy('user_id')
            ->map(fn($group) => $group->sum('quantite'))
            ->toArray();
    }

    protected function sendNotification($offre, $sender, $quantitesParUser)
    {
        try {
            DB::beginTransaction();

            Notification::send($sender, new OffreNegosDone([
                'quantite_totale' => array_sum($quantitesParUser),
                'details_par_user' => $quantitesParUser,
                'idProd' => $offre->produit_id,
                'id_sender' => $offre->sender->id ?? null,
                'code_unique' => $offre->code_unique,
            ]));

            Log::info('Notification sent to user:', ['sender' => $sender->id]);

            $offre->update(['notified' => true]);

            DB::commit();

            Log::info('Updated countdown notified status for unique code: ' . $offre->code_unique);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing countdown with code ' . $offre->code_unique . ': ' . $e->getMessage());
        }
    }
}
