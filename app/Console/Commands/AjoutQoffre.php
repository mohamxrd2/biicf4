<?php

namespace App\Console\Commands;

use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\OffreNegosDone;
use Illuminate\Console\Command;
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
        // Retrieve non-notified offer groups that are at least 1 minute old
        $offrecountdowns = Countdown::where('notified', false)
            ->where('difference', 'offregroupe') // Note: Ensure column name and value are correct
            ->where('created_at', '<=', now()->subMinutes(1))
            ->get();

        foreach ($offrecountdowns as $offre) {
            Log::info('Processing countdown with unique code: ' . $offre->code_unique);

            $uniqueCode = $offre->code_unique;
            $offreGroupeExistante = OffreGroupe::where('code_unique', $uniqueCode)->first();

            if (!$offreGroupeExistante) {
                Log::error('OffreGroupe not found for unique code: ' . $uniqueCode);
                continue;
            }

            $produitId = $offreGroupeExistante->produit_id;
            $sommeQuantites = OffreGroupe::where('code_unique', $uniqueCode)->sum('quantite');
            $produit = ProduitService::find($produitId);

            if (!$produit) {
                Log::error('Product not found for ID: ' . $produitId);
                continue;
            }

            $data = [
                'quantite' => $sommeQuantites,
                'produit_id' => $produit->id,
                'produit_name' => $produit->name,
                'code_unique' => $uniqueCode
            ];

            Log::info('Notification data: ', $data);

            $idsProprietaires = Consommation::where('name', $produit->name)
                ->where('id_user', '!=', $produit->user_id)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();

            Log::info('IDs to notify: ', $idsProprietaires);

            foreach ($idsProprietaires as $conso) {
                $owner = User::find($conso);

                if ($owner) {
                    Log::info('Sending notification to user ID: ' . $owner->id);
                    Notification::send($owner, new OffreNegosDone($data));
                } else {
                    Log::error('User not found for ID: ' . $conso);
                }
            }

            $offre->update(['notified' => true]);
        }

      
    }
}
