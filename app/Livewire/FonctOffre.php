<?php

namespace App\Livewire;

use App\Models\Consommation;
use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\OffreNotif;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class FonctOffre extends Component
{
    public $produit;
    public   $id;
    public function mount($id)
    {

        // Récupérer le produit ou échouer
        $this->produit = ProduitService::findOrFail($id);
    }

    public function sendoffre()
    {
        try {
            $user_id = Auth::guard('web')->id();

            // Récupérer l'ID du produit et la zone économique à partir du formulaire
            $produitId = $request->input('produit_id');
            $zone_economique = strtolower($request->input('zone_economique')); // Normaliser en minuscules
            $user = User::find($user_id);

            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
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
            $referenceProduit = $produit->reference;

            // Requête pour récupérer les IDs des propriétaires des consommations similaires
            $idsProprietaires = Consommation::where('reference', $referenceProduit)
                ->where('id_user', '!=', $user_id)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();

            if (empty($idsProprietaires)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans votre zone économique.');
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
                throw new Exception('Aucun utilisateur ne consomme ce produit dans votre zone économique.');
            }

            // Fusionner les deux tableaux d'IDs
            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            // Compter le nombre total d'IDs
            $totalNotifications = count($idsToNotify);

            // Envoyer une notification à chaque propriétaire
            foreach ($idsToNotify as $userId) {
                $user = User::find($userId);
                if ($user) {
                    Notification::send($user, new OffreNotif($produit));
                }
            }
            // Stocker un message dans la session flash avec le nombre total de notifications
            session()->flash('formSubmitted', "Notifications envoyées à $totalNotifications utilisateur(s).");
            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (ModelNotFoundException $e) {
            Log::error('Erreur lors de la récupération des données.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une donnée requise est introuvable.');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function render()
    {



        return view('livewire.fonct-offre');
    }
}
