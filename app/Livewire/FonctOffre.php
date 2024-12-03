<?php

namespace App\Livewire;

use App\Models\Consommation;
use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\OffreNotif;
use App\Notifications\OffreNotifGroup;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use Livewire\Component;

class FonctOffre extends Component
{
    public $produit;
    public   $id;
    public   $idsProprietaires;
    public   $nombreProprietaires;
    public   $nomFournisseurCount;
    public   $zoneEconomique;

    public function mount($id)
    {

        // Récupérer le produit ou échouer
        $this->produit = ProduitService::findOrFail($id);
        // Récupérer l'identifiant de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer les IDs des propriétaires des consommations similaires
        $this->idsProprietaires = Consommation::where('name', $this->produit->name)
            ->where('id_user', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // Compter le nombre d'IDs distincts
        $this->nombreProprietaires = count($this->idsProprietaires);

        // Récupérer les fournisseurs pour ce produit
        $nomFournisseur = ProduitService::where('name', $this->produit->name)
            ->where('user_id', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        $this->nomFournisseurCount = count($nomFournisseur);
    }

    public function sendOffre()
    {

        try {
            $user_id = Auth::guard('web')->id();

            // Récupérer le produit et la zone économique sélectionnée
            $produit = ProduitService::findOrFail($this->produit->id);
            $referenceProduit = $produit->reference;
            $zoneEconomique = strtolower($this->zoneEconomique);

            $user = Auth::user();
            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
            }

            // Récupérer les utilisateurs ayant consommé ce produit
            $idsProprietaires = Consommation::where('reference', $referenceProduit)
                ->where('id_user', '!=', $user_id)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();

            if (empty($idsProprietaires)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans votre zone économique.');
            }

            // Appliquer les filtres de zone économique
            $userAttributes = [
                'proximite' => strtolower($user->commune),
                'locale' => strtolower($user->ville),
                'departementale' => strtolower($user->departe),
                'nationale' => strtolower($user->country),
                'sous_regionale' => strtolower($user->sous_region),
                'continentale' => strtolower($user->continent),
            ];

            $appliedZoneValue = $userAttributes[$zoneEconomique] ?? null;

            $idsLocalite = User::whereIn('id', $idsProprietaires)
                ->where(function ($query) use ($appliedZoneValue) {
                    $query->orWhere('commune', $appliedZoneValue)
                        ->orWhere('ville', $appliedZoneValue)
                        ->orWhere('departe', $appliedZoneValue)
                        ->orWhere('country', $appliedZoneValue)
                        ->orWhere('sous_region', $appliedZoneValue)
                        ->orWhere('continent', $appliedZoneValue);
                })
                ->pluck('id')
                ->toArray();

            if (empty($idsLocalite)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans votre zone économique.');
            }

            // Fusionner les IDs et notifier
            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            $this->nombreProprietaires = count($idsToNotify);

            foreach ($idsToNotify as $userId) {
                $targetUser = User::find($userId);
                if ($targetUser) {
                    Notification::send($targetUser, new OffreNotif($produit));
                }
            }

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nombreProprietaires} utilisateur(s).",
            );
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function sendoffGrp()
    {
        $user_id = Auth::guard('web')->id();

        try {
            // Récupérer le produit et la zone économique sélectionnée
            $produit = ProduitService::findOrFail($this->produit->id);
            $referenceProduit = $produit->reference;
            $zoneEconomique = strtolower($this->zoneEconomique);

            $user = Auth::user();
            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
            }

            Log::info('Produit récupéré', [
                'produit_id' => $produit,
                'reference' => $referenceProduit,
            ]);

            // Générer un code unique
            $code_unique = $this->generateUniqueReference();

            // Récupérer les utilisateurs consommant ce produit
            $idsProprietaires = $this->getConsommateurs($referenceProduit, $user_id);
            if (empty($idsProprietaires)) {
                Log::warning('Aucun utilisateur consommateur trouvé', ['produit' => $referenceProduit]);
                return redirect()->back()->with('error', 'Aucun utilisateur ne consomme ce produit.');
            }

            // Appliquer le filtre de zone économique
            $appliedZoneValue = $this->getZoneValue($zoneEconomique, $user);
            if (!$appliedZoneValue) {
                Log::error('Zone économique invalide', ['zone' => $zoneEconomique]);
                return redirect()->back()->with('error', 'Zone économique invalide.');
            }

            // Filtrer les utilisateurs par zone
            $idsLocalite = $this->getUsersInZone($idsProprietaires, $appliedZoneValue);
            if (empty($idsLocalite)) {
                Log::warning('Aucun utilisateur trouvé dans la zone économique', [
                    'zone' => $zoneEconomique,
                    'value' => $appliedZoneValue,
                ]);
                return redirect()->back()->with('error', 'Aucun utilisateur trouvé dans votre zone économique.');
            }

            // Fusionner les IDs pour éviter les doublons
            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            Log::info('IDs à notifier', ['ids' => $idsToNotify]);

            // Envoyer des notifications
            $this->notifyUsers($idsToNotify, $produit, $code_unique);
            $this->nombreProprietaires = count($idsToNotify);

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nombreProprietaires} utilisateur(s).",
            );
            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur s\'est produite. Veuillez réessayer.');
        }
    }

    /**
     * Récupérer les IDs des consommateurs d'un produit.
     */
    private function getConsommateurs(string $referenceProduit, int $user_id): array
    {
        return Consommation::where('reference', $referenceProduit)
            ->where('id_user', '!=', $user_id)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();
    }

    /**
     * Récupérer la valeur de la zone économique en fonction de l'utilisateur.
     */
    private function getZoneValue(string $zoneEconomique, $user)
    {
        $mapping = [
            'proximite' => strtolower($user->commune),
            'locale' => strtolower($user->ville),
            'departementale' => strtolower($user->departe),
            'nationale' => strtolower($user->country),
            'sous_regionale' => strtolower($user->sous_region),
            'continentale' => strtolower($user->continent),
        ];
        return $mapping[$zoneEconomique] ?? null;
    }

    /**
     * Récupérer les IDs des utilisateurs dans une zone donnée.
     */
    private function getUsersInZone(array $idsProprietaires, string $appliedZoneValue): array
    {
        return User::whereIn('id', $idsProprietaires)
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
    }

    /**
     * Envoyer des notifications aux utilisateurs spécifiés.
     */
    private function notifyUsers(array $idsToNotify, ProduitService $produit, string $code_unique): void
    {
        foreach ($idsToNotify as $userId) {
            $user = User::find($userId);
            if ($user) {
                Notification::send($user, new OffreNotifGroup($produit, $code_unique));
                Log::info('Notification envoyée', ['user_id' => $userId]);
            } else {
                Log::warning('Utilisateur non trouvé pour notification', ['user_id' => $userId]);
            }
        }
    }

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }
    public function render()
    {
        return view('livewire.fonct-offre');
    }
}
