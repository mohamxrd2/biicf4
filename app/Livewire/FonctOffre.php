<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\ProduitService;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\Confirmation;
use App\Notifications\OffreNegosNotif;
use App\Notifications\OffreNotif;
use App\Notifications\OffreNotifGroup;
use App\Services\RecuperationTimer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Livewire\Component;

class FonctOffre extends Component
{
    public $produit;
    public   $id;
    public   $idsProprietaires;
    public   $nombreProprietaires;
    public   $nomFournisseurCount;
    public   $zoneEconomique;
    public   $quantite;
    public   $username;

    public $time;
    public $error;

    protected $recuperationTimer;

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }
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

        // Récupération de l'heure via le service
        $this->time = $this->recuperationTimer->getTime();
        $this->error = $this->recuperationTimer->error;
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

    public function sendoffneg()
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

            // Générer un code unique
            $code_unique = $this->generateUniqueReference();

            $offgroupe = OffreGroupe::create([
                'name' => $produit->name,
                'code_unique' => $code_unique,
                'produit_id' => $produit->id,
                'zone' => $zoneEconomique,
                'user_id' => $user_id,
                'differance' => 'grouper',
            ]);


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
    private function notifyUsers(array $idsToNotify,  $produit, string $code_unique): void
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

    public function sendoffreGrp()
    {
        try {


            $user_id = Auth::id();
            Log::info('Tentative de stockage de données', ['user_id' => $user_id]);

            // Recherche du produit et de l'utilisateur
            $produit = ProduitService::findOrFail($this->produit->id);
            $user = User::where('username', $this->username)->firstOrFail();
            $zoneKey = $this->mapEconomicZone($this->zoneEconomique, $produit->user);

            // Générer un code unique
            $uniqueCode = $this->generateUniqueReference();

            // Trouver les fournisseurs pertinents
            $suppliers = $this->findSuppliers($produit, $user_id, $zoneKey);

            if (empty($suppliers)) {
                return $this->handleNoSuppliers($produit, $zoneKey);
            }

            // Notifier les fournisseurs
            $this->notifySuppliers($suppliers, $produit, $this->quantite, $uniqueCode);

            // Notification de confirmation à l'utilisateur
            Notification::send($user, new Confirmation([
                'idProd' => $produit->id,
                'code_unique' => $uniqueCode,
                'title' => 'Confirmation de commande',
                'description' => 'La commande groupée des fournisseurs a été envoyée avec succès.',
            ]));
            event(new NotificationSent($user));

            // Enregistrement dans la table `OffreGroupe`
            $this->saveOffreGroupe([
                'quantite' => $this->quantite,
                'produit_id' => $produit->id,
                'zone_economique' => $this->zoneEconomique,
            ], $produit, $user_id, $uniqueCode);

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nomFournisseurCount} utilisateur(s).",
            );

            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors du stockage des données', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    private function mapEconomicZone($zoneEconomique, $user)
    {
        $zoneMapping = [
            'proximite' => 'commune',
            'locale' => 'ville',
            'departementale' => 'departe',
            'nationale' => 'pays',
            'sous_regionale' => 'sous_region',
            'continentale' => 'continent',
        ];

        $zoneKey = $zoneMapping[strtolower($zoneEconomique)] ?? null;
        if (!$zoneKey || !isset($user->$zoneKey)) {
            throw new InvalidArgumentException('Zone économique invalide.');
        }

        Log::info('Zone économique mappée', ['zone_key' => $zoneKey, 'value' => $user->$zoneKey]);
        return $zoneKey;
    }

    private function findSuppliers($produit, $userId, $zoneKey)
    {
        $suppliers = ProduitService::where('reference', $produit->reference)
            ->where('user_id', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->whereHas('user', fn($query) => $query->where($zoneKey, $produit->user->$zoneKey))
            ->pluck('user_id')
            ->toArray();

        Log::info('Fournisseurs trouvés', ['suppliers' => $suppliers]);
        return $suppliers;
    }

    private function notifySuppliers(array $suppliers, $produit, $quantite, $uniqueCode)
    {
        foreach ($suppliers as $supplierId) {
            $supplier = User::find($supplierId);
            if ($supplier) {
                Notification::send($supplier, new OffreNegosNotif([
                    'idProd' => $produit->id,
                    'produit_name' => $produit->name,
                    'quantite' => $quantite,
                    'code_unique' => $uniqueCode,
                ]));
                event(new NotificationSent($supplier));

                Log::info('Notification envoyée', ['supplier_id' => $supplierId]);
            }
        }
    }

    private function saveOffreGroupe($data, $produit, $userId, $uniqueCode)
    {
        OffreGroupe::create([
            'name' => $produit->name,
            'quantite' => $data['quantite'],
            'code_unique' => $uniqueCode,
            'produit_id' => $data['produit_id'],
            'zone' => $data['zone_economique'],
            'user_id' => $userId,
            'differance' => 'offregrouper',
        ]);

        Log::info('OffreGroupe enregistrée', [
            'name' => $produit->name,
            'code_unique' => $uniqueCode,
        ]);

        userquantites::create([
            'code_unique' => $uniqueCode,
            'user_id' => $userId,
            'localite' => $data['zone_economique'],
            'quantite' => $data['quantite'],
        ]);
    }



    public function render()
    {
        return view('livewire.fonct-offre');
    }
}
