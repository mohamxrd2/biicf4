<?php

namespace App\Console\Commands;

use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\ProduitService;
use App\Models\User;
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

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $offrecountdowns = OffreGroupe::where('count', true)
            ->where('created_at', '<=', now()->subMinutes(2))
            ->get();

        if ($offrecountdowns->isEmpty()) {
            Log::info('No countdowns found to process.');
            return;
        }

        Log::info('Found ' . $offrecountdowns->count() . ' countdowns to process.');

        foreach ($offrecountdowns as $offre) {
            Log::info('Processing countdown with unique code: ' . $offre->code_unique);


            // Récupérer les quantités par utilisateur dans userquantites
            $quantitesParUser = userquantites::where('code_unique', $offre->code_unique)
                ->get()
                ->groupBy('user_id')
                ->map(function ($group) {
                    return $group->sum('quantite');
                })
                ->toArray();

            Log::info('Quantities per user for unique code ' . $offre->code_unique . ': ' . json_encode($quantitesParUser));

            $sender = $offre->user;
            if (!$sender) {
                Log::error('Sender not found for countdown: ' . $offre->code_unique);
                continue;
            }

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
}
