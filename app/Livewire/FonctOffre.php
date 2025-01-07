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
    public $fournisseursFiltered = [];


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
        $this->fournisseursFiltered = User::whereIn('id', $nomFournisseur)
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

        $this->nomFournisseurCount = count($this->fournisseursFiltered);
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

            if (empty($this->idsToNotify)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans la zone ' . $this->zoneEconomique . '.');
            }

            // Envoyer les notifications uniquement aux utilisateurs filtrés
            foreach ($this->idsToNotify as $userId) {
                $targetUser = User::find($userId);
                if ($targetUser) {
                    Notification::send($targetUser, new OffreNotif($this->produit));
                    Log::info('Notification envoyée', ['user_id' => $userId, 'zone' => $this->zoneEconomique]);
                }
            }


            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nombreProprietaires} utilisateur(s) dans la zone {$this->zoneEconomique}."
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
        $this->timeServer();

        try {
            $user = Auth::user();
            if (!$user) {
                throw new Exception('Utilisateur non trouvé.');
            }

            // Charger les données avant de continuer
            $this->loadData();

            if (empty($this->idsToNotify)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans la zone ' . $this->zoneEconomique . '.');
            }

            // Générer un code unique
            $code_unique = $this->generateUniqueReference();

            // Créer l'offre de groupe
            $offgroupe = OffreGroupe::create([
                'name' => $this->produit->name,
                'code_unique' => $code_unique,
                'produit_id' => $this->produit->id,
                'zone' => $this->zoneEconomique,
                'user_id' => $user->id,
                'differance' => 'grouper',
                'notified' => true,
            ]);

            $difference = 'enchere';
            $this->startCountdown($code_unique, $difference);

            // Envoyer les notifications
            foreach ($this->idsToNotify as $userId) {
                $targetUser = User::find($userId);
                if ($targetUser) {
                    Notification::send($targetUser, new OffreNotifGroup($this->produit, $code_unique));
                    Log::info('Notification envoyée', ['user_id' => $userId]);
                } else {
                    Log::warning('Utilisateur non trouvé pour notification', ['user_id' => $userId]);
                }
            }

            $this->nombreProprietaires = count($this->idsToNotify);

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nombreProprietaires} utilisateur(s)."
            );

            session()->flash('success', 'Offre négociée envoyée avec succès !');
            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'offre négociée', ['error' => $e->getMessage()]);
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


    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function sendoffreGrp()
    {
        try {
            $this->validate();
            $this->timeServer();
            $this->loadData();

            $user_id = Auth::id();
            // Recherche du produit et de l'utilisateur
            $produit = ProduitService::findOrFail($this->produit->id);
            $user = User::where('username', $this->username)->firstOrFail();

            // Générer un code unique
            $uniqueCode = $this->generateUniqueReference();

            // Notification de confirmation à l'utilisateur
            Notification::send($user, new Confirmation([
                'idProd' => $produit->id,
                'code_unique' => $uniqueCode,
                'title' => 'Confirmation de commande',
                'description' => 'Votre commande de {} des fournisseurs a été envoyée avec succès.',
            ]));
            event(new NotificationSent($user));

            // Notifier les fournisseurs
            foreach ($this->fournisseursFiltered as $supplierId) {
                $supplier = User::find($supplierId);
                if ($supplier) {
                    Notification::send($supplier, new OffreNegosNotif([
                        'idProd' => $produit->id,
                        'produit_name' => $produit->name,
                        'quantite' => $this->quantite,
                        'code_unique' => $uniqueCode,
                    ]));

                    Log::info('Notification envoyée', ['supplier_id' => $supplierId]);
                }
                event(new NotificationSent($supplier));
            }


            // Enregistrement dans la table `OffreGroupe`
            $this->saveOffreGroupe([
                'quantite' => $this->quantite,
                'produit_id' => $produit->id,
                'zone_economique' => $this->zoneEconomique,
            ], $produit, $user_id, $user->id, $uniqueCode);


            $difference = 'offreGrouper';
            $this->startCountdown($uniqueCode, $difference);

            $this->dispatch(
                'formSubmitted',
                "Notifications envoyées à {$this->nomFournisseurCount} utilisateur(s).",
            );

            session()->flash('success', 'Offre groupée envoyée avec succès !');

            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
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
