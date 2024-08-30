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
        Log::info('Starting AjoutQoffre command.');

        $offrecountdowns = Countdown::where('notified', false)
            ->where('difference', 'offregroupe') // Ensure column name and value are correct
            ->where('created_at', '<=', now()->subMinutes(1))
            ->get();

        Log::info('Found ' . $offrecountdowns->count() . ' countdowns to process.');

        foreach ($offrecountdowns as $offre) {
            Log::info('Processing countdown with unique code: ' . $offre->code_unique);

            $uniqueCode = $offre->code_unique;
            $offreGroupeExistante = OffreGroupe::where('code_unique', $uniqueCode)->first();

            if (!$offreGroupeExistante) {
                Log::error('OffreGroupe not found for unique code: ' . $uniqueCode);
                continue;
            }

            $produitId = $offreGroupeExistante->produit_id;
            Log::info('Found OffreGroupe with produit ID: ' . $produitId);

            $sommeQuantites = OffreGroupe::where('code_unique', $uniqueCode)->sum('quantite');
            Log::info('Total quantity for OffreGroupe with code ' . $uniqueCode . ': ' . $sommeQuantites);

            $produit = ProduitService::find($produitId);
            if (!$produit) {
                Log::error('Product not found for ID: ' . $produitId);
                continue;
            }

            Log::info('Found product: ' . $produit->name . ' for ID: ' . $produitId);

            $user = User::find($produit->user_id);
            if (!$user) {
                Log::error('User not found for product ID: ' . $produit->user_id);
                continue;
            }

            Log::info('Found user for product: ' . $user->name . ' (ID: ' . $user->id . ')');

            // Determine the economic zone selected by the user
            $zone_economique = $offreGroupeExistante->zone; // Replace with your logic to determine the zone
            Log::info('Economic zone selected: ' . $zone_economique);

            $userZone = strtolower($user->commune);
            $userVille = strtolower($user->ville);
            $userDepartement = strtolower($user->departe);
            $userPays = strtolower($user->country);
            $userSousRegion = strtolower($user->sous_region);
            $userContinent = strtolower($user->continent);

            $appliedZoneValue = null;
            switch ($zone_economique) {
                case 'proximite':
                    $appliedZoneValue = $userZone;
                    break;
                case 'locale':
                    $appliedZoneValue = $userVille;
                    break;
                case 'departementale':
                    $appliedZoneValue = $userDepartement;
                    break;
                case 'nationale':
                    $appliedZoneValue = $userPays;
                    break;
                case 'sous_regionale':
                    $appliedZoneValue = $userSousRegion;
                    break;
                case 'continentale':
                    $appliedZoneValue = $userContinent;
                    break;
                default:
                    Log::warning('Unknown economic zone: ' . $zone_economique);
                    break;
            }

            Log::info('Applied zone value for notification: ' . $appliedZoneValue);

            // Retrieve IDs of users in the same locality who have the product
            $idsProprietaires = Consommation::where('name', $produit->name)
                ->where('id_user', '!=', $produit->user_id)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();

            Log::info('IDs of product owners to notify: ', $idsProprietaires);

            $idsLocalite = User::whereIn('id', $idsProprietaires)
                ->where(function ($query) use ($appliedZoneValue) {
                    $query->where('commune', $appliedZoneValue)
                        ->orWhere('ville', $appliedZoneValue)
                        ->orWhere('departe', $appliedZoneValue)
                        ->orWhere('country', $appliedZoneValue)
                        ->orWhere('sous_region', $appliedZoneValue)
                        ->orWhere('continent', $appliedZoneValue);
                })
                ->pluck('id')
                ->toArray();

            Log::info('IDs of users in the same locality retrieved:', ['ids_localite' => $idsLocalite]);

            if (empty($idsLocalite)) {
                Log::error('No users consume this product in your economic zone.');
                continue;
            }

            // Merge the two ID arrays
            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            Log::info('Final list of user IDs to notify: ', $idsToNotify);

            // Send a notification to each user
            foreach ($idsToNotify as $userId) {
                $user = User::find($userId);
                if ($user) {
                    try {
                        Notification::send($user, new OffreNegosDone([
                            'quantite' => $sommeQuantites,
                            'produit_id' => $produit->id,
                            'produit_name' => $produit->name,
                            'code_unique' => $uniqueCode
                        ]));
                        Log::info('Notification sent to user:', ['user_id' => $userId]);
                    } catch (\Exception $e) {
                        Log::error('Error sending notification:', ['user_id' => $userId, 'exception' => $e->getMessage()]);
                    }
                } else {
                    Log::error('User not found for ID: ' . $userId);
                }
            }

            // Update the notification status
            $offre->update(['notified' => true]);
            Log::info('Updated countdown notified status for unique code: ' . $uniqueCode);
        }

        Log::info('AjoutQoffre command finished.');
    }
}
