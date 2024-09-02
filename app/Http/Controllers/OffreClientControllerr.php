<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Consommation;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use App\Notifications\OffreNotif;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OffreClientControllerr extends Controller
{
    public function sendoffre(Request $request)
    {

        $user_id = Auth::guard('web')->id();

        // Récupérer l'ID du produit et la zone économique à partir du formulaire
        $produitId = $request->input('produit_id');
        $zone_economique = strtolower($request->input('zone_economique')); // Normaliser en minuscules
        $user = User::find($user_id);

        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé.');
        }

        // Déterminer la zone économique choisie par l'utilisateur
        $userZone = strtolower($user->commune);
        $userVille = strtolower($user->ville);
        $userDepartement = strtolower($user->departe);
        $userPays = strtolower($user->country);
        $userSousRegion = strtolower($user->sous_region);
        $userContinent = strtolower($user->continent);

        // Trouver le produit ou échouer
        $produit = ProduitService::findOrFail($produitId);
        $nomProduit = $produit->name;

        // Générer un code unique une seule fois pour tous les utilisateurs
        // $Uniquecode = $this->genererCodeAleatoire(10);

        // Requête pour récupérer les IDs des propriétaires des consommations similaires
        $idsProprietaires = Consommation::where('name', $nomProduit)
            ->where('id_user', '!=', $user_id)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        if (empty($idsProprietaires)) {
            return redirect()->back()->with('error', 'Aucun utilisateur ne consomme ce produit dans votre zone économique.');
        }

        // Appliquer le filtre de zone économique
        $appliedZoneValue = null;
        if ($zone_economique === 'proximite') {
            $appliedZoneValue = $userZone;
        } elseif ($zone_economique === 'locale') {
            $appliedZoneValue = $userVille;
        } elseif ($zone_economique === 'departementale') {
            $appliedZoneValue = $userDepartement;
        } elseif ($zone_economique === 'nationale') {
            $appliedZoneValue = $userPays;
        } elseif ($zone_economique === 'sous_regionale') {
            $appliedZoneValue = $userSousRegion;
        } elseif ($zone_economique === 'continentale') {
            $appliedZoneValue = $userContinent;
        }

        // Récupérer les IDs des utilisateurs dans la zone choisie
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

        Log::info('IDs des utilisateurs avec la même localité récupérés:', ['ids_localite' => $idsLocalite]);

        if (empty($idsLocalite)) {
            return redirect()->back()->with('error', 'Aucun utilisateur ne consomme ce produit dans votre zone économique.');
        }

        // Fusionner les deux tableaux d'IDs
        $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));

        // Envoyer une notification à chaque propriétaire
        foreach ($idsToNotify as $userId) {
            $user = User::find($userId);
            if ($user) {
                Notification::send($user, new OffreNotif($produit));
            }
        }

        return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
    }
}
