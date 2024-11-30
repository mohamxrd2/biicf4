<?php

namespace App\Console\Commands;

use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\ProduitService;
use App\Models\User;
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


        $offrecountdowns = Countdown::where('notified', false)
            ->where('difference', 'offregroupe')
            ->where('created_at', '<=', now()->subMinutes(2))
            ->get();

        if ($offrecountdowns->isEmpty()) {
            Log::info('No countdowns found to process.');
            return;
        }

        Log::info('Found ' . $offrecountdowns->count() . ' countdowns to process.');

        foreach ($offrecountdowns as $offre) {
            Log::info('Processing countdown with unique code: ' . $offre->code_unique);

            $uniqueCode = $offre->code_unique;
            try {
                $offreGroupeExistante = OffreGroupe::where('code_unique', $uniqueCode)->first();
            } catch (\Exception $e) {
                Log::error('Error fetching OffreGroupe for unique code ' . $uniqueCode . ': ' . $e->getMessage());
                continue;
            }

            if (!$offreGroupeExistante) {
                Log::error('OffreGroupe not found for unique code: ' . $uniqueCode);
                continue;
            }


            $sommeQuantites = OffreGroupe::where('code_unique', $uniqueCode)->sum('quantite');

            Log::info('Total quantity for OffreGroupe with code ' . $uniqueCode . ': ' . $sommeQuantites);


            $sender = $offre->user;
            if (!$sender) {
                Log::error('Sender not found for countdown: ' . $offre->code_unique);
                continue;
            }

            try {
                DB::beginTransaction();

                Notification::send($sender, new OffreNegosDone([
                    'quantite' => $sommeQuantites,
                    'idProd' =>  $offreGroupeExistante->produit_id,
                    'id_sender' => $offre->sender->id,
                    'code_unique' => $uniqueCode,
                ]));
                Log::info('Notification sent to user:', ['sender' => $sender]);

                $offre->update(['notified' => true]);
                DB::commit();

                Log::info('Updated countdown notified status for unique code: ' . $uniqueCode);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing countdown with code ' . $uniqueCode . ': ' . $e->getMessage());
            }
        }


        Log::info('AjoutQoffre command finished.');
    }
}
