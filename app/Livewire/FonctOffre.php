<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
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
use App\Services\TimeSync\TimeSyncService;
use Carbon\Carbon;
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
    public $nombreProprietaires = 0;
    public   $nomFournisseurCount;
    public $zoneEconomique = 'proximite'; // Valeur par défaut
    public   $quantite;
    public   $username;
    public   $idsToNotify;

    public $time;
    public $error;
    public $timestamp;
    public $countdownId;
    public $isRunning;
    public $timeRemaining;
    protected $recuperationTimer;

    protected $rules = [
        'quantite' => 'required|numeric|min:1',
        'username' => 'required|exists:users,username',
        'zoneEconomique' => 'required|in:proximite,locale,departementale,nationale,sous_regionale,continentale',
    ];

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }
    public function mount($id)
    {
        $this->timeServer();
        $this->produit = ProduitService::findOrFail($id);

        // Initialiser idsProprietaires avant loadData()
        $this->idsProprietaires = [];

        $this->time = $this->recuperationTimer->getTime();
        $this->loadData();
    }

    public function updatedZoneEconomique($value)
    {
        $this->loadData();
    }

    public function loadData()
    {
        $userId = Auth::guard('web')->id();
        $user = Auth::user();
        $referenceProduit = $this->produit->reference;

        $zoneEconomique = strtolower($this->zoneEconomique);

        // Récupérer tous les consommateurs du produit
        $this->idsProprietaires = Consommation::where('reference', $referenceProduit)
            ->where('id_user', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // Filtrer par zone économique
        $userAttributes = [
            'proximite' => strtolower($user->commune),
            'locale' => strtolower($user->ville),
            'departementale' => strtolower($user->departe),
            'nationale' => strtolower($user->country),
            'sous_regionale' => strtolower($user->sous_region),
            'continentale' => strtolower($user->continent),
        ];

        $appliedZoneValue = $userAttributes[$zoneEconomique] ?? null;

        // Filtrer les utilisateurs selon la zone sélectionnée
        $this->idsToNotify = User::whereIn('id', $this->idsProprietaires)
            ->where(function ($query) use ($appliedZoneValue, $zoneEconomique) {
                $column = match ($zoneEconomique) {
                    'proximite' => 'commune',
                    'locale' => 'ville',
                    'departementale' => 'departe',
                    'nationale' => 'country',
                    'sous_regionale' => 'sous_region',
                    'continentale' => 'continent',
                    default => 'commune'
                };
                $query->where($column, $appliedZoneValue);
            })
            ->pluck('id')
            ->toArray();

        // Mettre à jour le nombre de propriétaires
        $this->nombreProprietaires = count($this->idsToNotify);

        // Récupérer les fournisseurs pour ce produit
        $nomFournisseur = ProduitService::where('name', $this->produit->name)
            ->where('user_id', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        // Filtrer les fournisseurs par zone
        $fournisseursFiltered = User::whereIn('id', $nomFournisseur)
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

        $this->nomFournisseurCount = count($fournisseursFiltered);
    }

    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();
        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }
    public function sendOffre()
    {
        try {
            $user_id = Auth::guard('web')->id();
            $user = Auth::user();

            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
            }

            // Récupérer le produit
            $produit = ProduitService::findOrFail($this->produit->id);
            $zoneEconomique = strtolower($this->zoneEconomique);

            // Récupérer la valeur de la zone pour l'utilisateur courant
            $userAttributes = [
                'proximite' => strtolower($user->commune),
                'locale' => strtolower($user->ville),
                'departementale' => strtolower($user->departe),
                'nationale' => strtolower($user->country),
                'sous_regionale' => strtolower($user->sous_region),
                'continentale' => strtolower($user->continent),
            ];

            $appliedZoneValue = $userAttributes[$zoneEconomique] ?? null;

            if (!$appliedZoneValue) {
                throw new Exception('Zone économique invalide.');
            }

            // Récupérer les consommateurs du produit dans la zone spécifique
            $idsToNotify = User::whereIn('id', function($query) use ($produit, $user_id) {
                $query->select('id_user')
                    ->from('consommations')
                    ->where('reference', $produit->reference)
                    ->where('id_user', '!=', $user_id)
                    ->where('statuts', 'Accepté')
                    ->distinct();
            })
            ->where(function ($query) use ($zoneEconomique, $appliedZoneValue) {
                $column = match($zoneEconomique) {
                    'proximite' => 'commune',
                    'locale' => 'ville',
                    'departementale' => 'departe',
                    'nationale' => 'country',
                    'sous_regionale' => 'sous_region',
                    'continentale' => 'continent',
                    default => 'commune'
                };
                $query->where($column, $appliedZoneValue);
            })
            ->pluck('id')
            ->toArray();

            if (empty($idsToNotify)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans la zone ' . $zoneEconomique . '.');
            }

            // Envoyer les notifications uniquement aux utilisateurs filtrés
            foreach ($idsToNotify as $userId) {
                $targetUser = User::find($userId);
                if ($targetUser) {
                    Notification::send($targetUser, new OffreNotif($produit));
                    Log::info('Notification envoyée', ['user_id' => $userId, 'zone' => $zoneEconomique]);
                }
            }

            $this->nombreProprietaires = count($idsToNotify);

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nombreProprietaires} utilisateur(s) dans la zone {$zoneEconomique}."
            );

            session()->flash('success', 'Offre envoyée avec succès !');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des offres', [
                'error' => $e->getMessage(),
                'zone' => $this->zoneEconomique ?? 'non définie'
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function sendoffneg()
    {
        try {
            $this->timeServer();
            $user_id = Auth::guard('web')->id();
            $user = Auth::user();

            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
            }

            // Récupérer le produit
            $produit = ProduitService::findOrFail($this->produit->id);
            $zoneEconomique = strtolower($this->zoneEconomique);

            // Récupérer la valeur de la zone pour l'utilisateur courant
            $userAttributes = [
                'proximite' => strtolower($user->commune),
                'locale' => strtolower($user->ville),
                'departementale' => strtolower($user->departe),
                'nationale' => strtolower($user->country),
                'sous_regionale' => strtolower($user->sous_region),
                'continentale' => strtolower($user->continent),
            ];

            $appliedZoneValue = $userAttributes[$zoneEconomique] ?? null;

            if (!$appliedZoneValue) {
                throw new Exception('Zone économique invalide.');
            }

            // Générer un code unique
            $code_unique = $this->generateUniqueReference();

            // Créer l'offre groupée
            OffreGroupe::create([
                'name' => $produit->name,
                'code_unique' => $code_unique,
                'produit_id' => $produit->id,
                'zone' => $zoneEconomique,
                'user_id' => $user_id,
                'differance' => 'grouper',
                'notified' => true,
            ]);

            // Récupérer les consommateurs du produit dans la zone spécifique
            $idsToNotify = User::whereIn('id', function($query) use ($produit, $user_id) {
                $query->select('id_user')
                    ->from('consommations')
                    ->where('reference', $produit->reference)
                    ->where('id_user', '!=', $user_id)
                    ->where('statuts', 'Accepté')
                    ->distinct();
            })
            ->where(function ($query) use ($zoneEconomique, $appliedZoneValue) {
                $column = match($zoneEconomique) {
                    'proximite' => 'commune',
                    'locale' => 'ville',
                    'departementale' => 'departe',
                    'nationale' => 'country',
                    'sous_regionale' => 'sous_region',
                    'continentale' => 'continent',
                    default => 'commune'
                };
                $query->where($column, $appliedZoneValue);
            })
            ->pluck('id')
            ->toArray();

            if (empty($idsToNotify)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans la zone ' . $zoneEconomique . '.');
            }

            // Démarrer le countdown
            $difference = 'enchere';
            $this->startCountdown($code_unique, $difference);

            // Envoyer les notifications
            foreach ($idsToNotify as $userId) {
                $targetUser = User::find($userId);
                if ($targetUser) {
                    Notification::send($targetUser, new OffreNegosNotif($produit, $code_unique));
                    Log::info('Notification envoyée', ['user_id' => $userId, 'zone' => $zoneEconomique]);
                }
            }

            $this->nombreProprietaires = count($idsToNotify);

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nombreProprietaires} utilisateur(s) dans la zone {$zoneEconomique}."
            );

            session()->flash('success', 'Offre négociée envoyée avec succès !');
            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des offres négociées', [
                'error' => $e->getMessage(),
                'zone' => $this->zoneEconomique ?? 'non définie'
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur s\'est produite. Veuillez réessayer.');
        }
    }

    public function startCountdown($code_unique, $difference)
    {
        // Utiliser firstOrCreate pour éviter les doublons
        $countdown = Countdown::firstOrCreate(

            [
                'code_unique' => $code_unique,
                'is_active' => true
            ],
            [
                'user_id' => Auth::id(),
                'start_time' => $this->timestamp,
                'difference' => $difference,
                'time_remaining' => 120,
                'end_time' => $this->timestamp->addMinutes(2),
                'is_active' => true,
            ]
        );

        if ($countdown->wasRecentlyCreated) {
            $this->countdownId = $countdown->id;
            $this->isRunning = true;
            $this->timeRemaining = 120;

            // Dispatch le job immédiatement
            dispatch(new ProcessCountdown($countdown->id, $code_unique))
                ->onQueue('default')
                ->afterCommit();

            event(new CountdownStarted(120, $code_unique));
        }
    }
    private function getConsommateurs(string $referenceProduit, int $user_id): array
    {
        return Consommation::where('reference', $referenceProduit)
            ->where('id_user', '!=', $user_id)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();
    }

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
            $this->validate();
            $this->timeServer();

            $user_id = Auth::id();
            $user = Auth::user();

            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
            }

            // Récupérer le produit et l'utilisateur cible
            $produit = ProduitService::findOrFail($this->produit->id);
            $targetUser = User::where('username', $this->username)->firstOrFail();
            $zoneEconomique = strtolower($this->zoneEconomique);

            // Récupérer la valeur de la zone
            $userAttributes = [
                'proximite' => strtolower($user->commune),
                'locale' => strtolower($user->ville),
                'departementale' => strtolower($user->departe),
                'nationale' => strtolower($user->country),
                'sous_regionale' => strtolower($user->sous_region),
                'continentale' => strtolower($user->continent),
            ];

            $appliedZoneValue = $userAttributes[$zoneEconomique] ?? null;

            if (!$appliedZoneValue) {
                throw new Exception('Zone économique invalide.');
            }

            // Générer un code unique
            $uniqueCode = $this->generateUniqueReference();

            // Trouver les fournisseurs dans la zone spécifique
            $suppliers = User::whereIn('id', function($query) use ($produit, $user_id) {
                $query->select('user_id')
                    ->from('produit_services')
                    ->where('reference', $produit->reference)
                    ->where('user_id', '!=', $user_id)
                    ->where('statuts', 'Accepté')
                    ->distinct();
            })
            ->where(function ($query) use ($zoneEconomique, $appliedZoneValue) {
                $column = match($zoneEconomique) {
                    'proximite' => 'commune',
                    'locale' => 'ville',
                    'departementale' => 'departe',
                    'nationale' => 'country',
                    'sous_regionale' => 'sous_region',
                    'continentale' => 'continent',
                    default => 'commune'
                };
                $query->where($column, $appliedZoneValue);
            })
            ->pluck('id')
            ->toArray();

            if (empty($suppliers)) {
                throw new Exception('Aucun fournisseur trouvé dans la zone ' . $zoneEconomique . '.');
            }

            // Créer l'offre groupée
            OffreGroupe::create([
                'name' => $produit->name,
                'code_unique' => $uniqueCode,
                'produit_id' => $produit->id,
                'zone' => $zoneEconomique,
                'user_id' => $user_id,
                'client_id' => $targetUser->id,
                'quantite' => $this->quantite,
                'differance' => 'offreGrouper',
                'notified' => true,
            ]);

            // Notifier les fournisseurs
            foreach ($suppliers as $supplierId) {
                $supplier = User::find($supplierId);
                if ($supplier) {
                    Notification::send($supplier, new OffreNotifGroup($produit, $uniqueCode));
                    Log::info('Notification envoyée au fournisseur', ['supplier_id' => $supplierId, 'zone' => $zoneEconomique]);
                }
            }

            // Notifier l'utilisateur cible
            Notification::send($targetUser, new Confirmation([
                'idProd' => $produit->id,
                'code_unique' => $uniqueCode,
                'title' => 'Confirmation de commande',
                'description' => 'La commande groupée a été envoyée aux fournisseurs de votre zone.',
            ]));

            // Démarrer le countdown
            $this->startCountdown($uniqueCode, 'offreGrouper');

            $this->nomFournisseurCount = count($suppliers);

            $this->dispatch(
                'formSubmitted',
                "Offre groupée envoyée à {$this->nomFournisseurCount} fournisseur(s) dans la zone {$zoneEconomique}."
            );

            session()->flash('success', 'Offre groupée envoyée avec succès !');
            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'offre groupée', [
                'error' => $e->getMessage(),
                'zone' => $this->zoneEconomique ?? 'non définie'
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
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

                Log::info('Notification envoyée', ['supplier_id' => $supplierId]);
            }
            event(new NotificationSent($supplier));
        }
    }

    private function saveOffreGroupe($data, $produit, $userId, $user, $uniqueCode)
    {
        OffreGroupe::create([
            'name' => $produit->name,
            'quantite' => $data['quantite'],
            'code_unique' => $uniqueCode,
            'produit_id' => $data['produit_id'],
            'zone' => $data['zone_economique'],
            'user_id' => $userId,
            'client_id' => $user,
            'differance' => 'offregrouper',
            'notified' => false,
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
