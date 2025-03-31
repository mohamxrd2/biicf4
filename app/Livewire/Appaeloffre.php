<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\AppelOffreGrouper;
use App\Models\AppelOffreUser;
use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\AOGrouper;
use App\Notifications\AppelOffre;
use App\Notifications\Confirmation;
use App\Services\generateIntegerReference;
use App\Services\generateUniqueReference;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Str;

class Appaeloffre extends Component
{

    public $distinctquatiteMax, $distinctquatiteMin, $name, $reference, $distinctSpecifications = [];
    public $appliedZoneValue, $quantité, $localite, $selectedOption, $dateTot, $dateTard;
    public $timeStart, $timeEnd, $dayPeriod, $dayPeriodFin, $id, $prodUsers = [];
    public $time, $error, $timestamp, $countdownId, $isRunning, $loading = false;
    public $timeRemaining, $distinctCondProds, $lowestPricedProduct, $wallet, $type, $produitExiste;

    protected $recuperationTimer, $referenceService, $reference_service, $TransactionService;

    // Constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
        // Instancier les services en dehors des propriétés Livewire
        $this->referenceService = new generateUniqueReference();
        $this->reference_service = new generateIntegerReference();
        $this->TransactionService = new TransactionService();
    }

    public function mount(
        $wallet,
        $lowestPricedProduct,
        $distinctCondProds,
        $type,
        $prodUsers,
        $distinctquatiteMax,
        $distinctquatiteMin,
        $name,
        $reference,
        $distinctSpecifications,
        $appliedZoneValue
    ) {
        $this->timeServer();

        $this->wallet = $wallet;
        $this->lowestPricedProduct = $lowestPricedProduct;
        $this->distinctCondProds = $distinctCondProds;
        $this->type = $type;
        $this->prodUsers = $prodUsers;
        $this->distinctquatiteMax = $distinctquatiteMax;
        $this->distinctquatiteMin = $distinctquatiteMin;
        $this->name = $name;
        $this->reference = $reference;
        $this->distinctSpecifications = is_array($distinctSpecifications) ? implode(', ', $distinctSpecifications) : $distinctSpecifications;
        $this->appliedZoneValue = $appliedZoneValue;


        $this->id = ProduitService::where('reference', $reference)->first();

        $this->produitExiste = $this->id ? Consommation::where('reference', $this->id->reference)->exists() : false;
    }



    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();
        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }

    public function startCountdown($code_unique, $difference, $AppelOffreGrouper_id, $id)
    {
        try {
            $countdown = Countdown::firstOrCreate(
                [
                    'code_unique' => $code_unique,
                    'is_active' => true
                ],
                [
                    'user_id' => Auth::id(),
                    'userSender' => null,
                    'start_time' => $this->timestamp,
                    'difference' => $difference,
                    $AppelOffreGrouper_id => $id,
                    'time_remaining' => 120,
                    'end_time' => $this->timestamp->addMinutes(2),
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
        } catch (Exception $e) {
            Log::error('Error starting countdown', [
                'error' => $e->getMessage(),
                'code_unique' => $code_unique
            ]);
        }
    }
    private function createAppelOffre($validatedData, $userId, $code_unique)
    {
        // Étape 2 : Calculer le coût total
        $totalCost = $validatedData['quantité'] * $this->lowestPricedProduct;

        // Étape 3 : Vérifier le solde disponible
        if ($this->wallet->balance < $totalCost) {
            session()->flash('error', "Solde insuffisant pour effectuer cette transaction.");
            throw new Exception("Solde insuffisant.");
        }

        // Décrémenter le solde
        $this->wallet->decrement('balance', $totalCost);


        // Créer un appel d'offre
        $appelOffre = AppelOffreUser::create([
            'product_name' => $this->name,
            'quantity' => $validatedData['quantité'],
            'payment' => 'comptant',
            'livraison' => $validatedData['selectedOption'],
            'date_tot' => $validatedData['dateTot'],
            'date_tard' => $validatedData['dateTard'],
            'time_start' => null,
            'time_end' => null,
            'day_period' => null,
            'day_periodFin' => null,
            'specification' => $this->distinctSpecifications,
            'reference' => $this->reference,
            'montant_total' => $totalCost,
            'localite' => $validatedData['localite'],
            'id_prod' => $this->id,
            'code_unique' => $code_unique,
            'lowestPricedProduct' => $this->lowestPricedProduct,
            'prodUsers' => json_encode($this->prodUsers),
            'image' => $validatedData['image'] ?? null,
            'id_sender' => $userId,
        ]);

        // Créer une transaction
        $this->TransactionService->createTransaction(
            $userId,
            $userId,
            'Gele',
            $totalCost,
            $this->reference_service->generate(),
            'Gele Pour Achat de ' . $this->name,
            'COC'
        );;

        // Mettre à jour la table des gels
        gelement::create([
            'id_wallet' => $this->wallet->id,
            'amount' => $totalCost,
            'reference_id' => $code_unique,
        ]);

        return $appelOffre;
    }
    private function notifyUsers($appelOffre)
    {
        $notified = false; // Variable pour vérifier si au moins un fournisseur est notifié

        Log::info("Début de la notification pour l'appel d'offre ID: {$appelOffre->id}");

        foreach ($this->prodUsers as $prodUser) {
            $owner = User::find($prodUser);

            if ($owner) {
                $produit = $owner->produitService()->first(); // Récupérer le premier produit lié

                if (!$produit) {
                    Log::warning("Utilisateur ID: {$owner->id} n'a pas de produit associé.");
                    continue; // Passer à l'utilisateur suivant
                }

                $quantiteUserMin = $produit->qteProd_min ?? 0;
                $quantiteUserMax = $produit->qteProd_max ?? 0;
                $quantiteAppelOffre = $appelOffre->quantity;

                Log::info("Utilisateur ID: {$owner->id}, Quantité min: {$quantiteUserMin}, Quantité max: {$quantiteUserMax}, Quantité requise: {$quantiteAppelOffre}");

                // Vérifier si la quantité demandée est comprise entre min et max
                if ($quantiteUserMin <= $quantiteAppelOffre && $quantiteAppelOffre <= $quantiteUserMax) {
                    $data = [
                        'id_appelOffre' => $appelOffre->id,
                        'code_unique' => $appelOffre->code_unique,
                        'type_achat' => $this->selectedOption,
                    ];

                    Notification::send($owner, new AppelOffre($data));
                    event(new NotificationSent($owner));

                    Log::info("Notification envoyée à l'utilisateur ID: {$owner->id}");


                    $notified = true; // Un fournisseur a été notifié
                } else {
                    Log::info("Utilisateur ID: {$owner->id} ignoré, quantité hors de sa tranche.");
                }
            } else {
                Log::warning("Utilisateur ID: {$prodUser} introuvable.");
            }
        }

        // Notification pour l'utilisateur actuel
        Notification::send(auth()->user(), new Confirmation([
            'code_unique' => $this->referenceService->generate(),
            'Id' => $appelOffre->id,
            'title' => 'Confirmation de commande',
            'description' => 'Cliquez pour voir les détails.',
        ]));
        // Vérifier si aucun fournisseur n'a été notifié
        if (!$notified) {
            $message = "Aucun fournisseur ne répond aux critères de quantité.";
            session()->flash('warning', $message);
            Log::warning($message);
        }

        Log::info("Fin de la notification pour l'appel d'offre ID: {$appelOffre->id}");
    }


    public function submitGroupe()
    {
        // Actualiser le timer avant de commencer
        $this->timeServer();

        // Vérifier si une zone économique est sélectionnée
        if (!$this->appliedZoneValue) {
            Log::warning('Aucune zone économique sélectionnée.');
            session()->flash('error', 'Veuillez sélectionner une zone économique pour pouvoir vous grouper.');
            return;
        }

        $this->loading = true;
        $userId = Auth::guard('web')->id(); // ID de l'utilisateur connecté

        DB::beginTransaction();

        try {
            // Validation des données
            $validatedData = $this->validate([
                'name' => 'required|string|max:255',
                'quantité' => 'required|integer|min:1',
                'selectedOption' => 'required|string|max:255',
                'dateTot' => 'required|date|before_or_equal:dateTard',
                'dateTard' => 'required|date|after_or_equal:dateTot',
                'localite' => 'required|string|max:255',
                'appliedZoneValue' => 'required|string|max:255',
                'prodUsers' => 'required|array|min:1',
            ]);

            // Générer un code unique une seule fois
            $codeUnique = $this->referenceService->generate();

            // Création de l'appel d'offre groupé
            $offre = AppelOffreGrouper::create([
                'lowestPricedProduct' => $this->lowestPricedProduct,
                'productName' => $validatedData['name'],
                'quantity' => $validatedData['quantité'],
                'payment' => 'comptant',
                'Livraison' => $validatedData['selectedOption'],
                'dateTot' => $validatedData['dateTot'],
                'dateTard' => $validatedData['dateTard'],
                // 'specificity' => $validatedData['distinctSpecifications'],
                'localite' => $validatedData['localite'],
                'image' => $validatedData['image'] ?? null,
                'prodUsers' => json_encode($validatedData['prodUsers']),
                'codeunique' => $codeUnique,
                'reference' => $this->reference,
                'user_id' => $userId,
                'started_at' => $this->timestamp,
            ]);

            // Enregistrer la quantité utilisateur
            userquantites::create([
                'user_id' => $userId,
                'localite' => $validatedData['localite'],
                'quantite' => $validatedData['quantité'],
                'code_unique' => $codeUnique,
            ]);

            // Calcul du coût total
            $totalCost = $validatedData['quantité'] * $this->lowestPricedProduct;

            // Vérifier et décrémenter le solde du portefeuille
            if ($this->wallet->balance < $totalCost) {
                Log::error('Solde insuffisant.', ['balance' => $this->wallet->balance, 'total_cost' => $totalCost]);
                throw new Exception("Votre solde est insuffisant pour effectuer cette transaction.");
            }

            $this->wallet->decrement('balance', $totalCost);

            // Enregistrer la transaction
            $this->TransactionService->createTransaction(
                $userId,
                $userId,
                'Gele',
                $totalCost,
                $this->reference_service->generate(),
                'Gele pour groupage de ' . $validatedData['name'],
                'COC'
            );

            // Enregistrer le gel des fonds
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $totalCost,
                'reference_id' => $codeUnique,
            ]);


            $difference = 'quantiteGrouper';
            $AppelOffreGrouper_id = 'AppelOffreGrouper_id';
            $id = $offre->id;
            $this->startCountdown($codeUnique, $difference, $AppelOffreGrouper_id, $id);

            // Notifications aux utilisateurs intéressés
            $idsProprietaires = Consommation::where('name', $offre->productName)
                ->where('id_user', '!=', $userId)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();

            $idsLocalite = User::whereIn('id', $idsProprietaires)
                ->where(function ($query) use ($validatedData) {
                    $query->where('continent', $validatedData['appliedZoneValue'])
                        ->orWhere('sous_region', $validatedData['appliedZoneValue'])
                        ->orWhere('country', $validatedData['appliedZoneValue'])
                        ->orWhere('departe', $validatedData['appliedZoneValue'])
                        ->orWhere('ville', $validatedData['appliedZoneValue'])
                        ->orWhere('commune', $validatedData['appliedZoneValue']);
                })
                ->pluck('id')
                ->toArray();

            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            if (empty($idsToNotify)) {
                throw new Exception('Aucun utilisateur ne consomme ce produit dans votre zone économique.');
            }

            foreach ($idsToNotify as $id) {
                $user = User::find($id);
                if ($user) {
                    Notification::send($user, new AOGrouper($offre->codeunique, $offre->id));
                    event(new NotificationSent($user));
                    Log::info('Notification envoyée.', ['user_id' => $id]);
                }
            }

            // Notification pour l'utilisateur actuel
            Notification::send(auth()->user(), new Confirmation([
                'code_unique' => $this->referenceService->generate(),
                'Id' => $offre->id,
                'title' => 'Confirmation de commande',
                'description' => 'Cliquez pour voir les détails.',
            ]));

            Log::info('Notification de confirmation envoyée.');
            $user_connecte = User::find(Auth::id());
            event(new NotificationSent($user_connecte));
            // Redirection ou traitement pour l'envoi direct
            $this->dispatch(
                'formSubmitted',
                'Demande d\'appel offre grouper a été effectué avec succes.'
            );
            Log::info('Fin du processus de création de l\'appel d\'offre groupé.');
            DB::commit();
            Log::info('Transaction DB validée.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur dans le regroupement.', ['error' => $e->getMessage()]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }
    public function submitEnvoie()
    {
        // Actualiser le timer avant de commencer
        $this->timeServer();

        $this->loading = true;
        $userId = Auth::id();
        // Étape 1 : Valider les données du formulaire
        $validatedData = $this->validate([
            'quantité' => 'required|integer|min:1',
            'localite' => 'required|string|max:255',
            'selectedOption' => 'required|string',
            'dateTot' => 'required|date|before_or_equal:dateTard',
            'dateTard' => 'required|date|after_or_equal:dateTot',
        ], [
            'quantité.required' => 'La quantité est obligatoire.',
            'quantité.integer' => 'La quantité doit être un nombre entier.',
            'quantité.min' => 'La quantité doit être supérieure à 0.',
            'localite.required' => 'La localité est obligatoire.',
            'localite.string' => 'La localité doit être une chaîne de caractères.',
            'localite.max' => 'La localité ne doit pas dépasser 255 caractères.',
            'selectedOption.required' => 'Le mode de livraison est obligatoire.',
            'selectedOption.string' => 'Le mode de livraison doit être une chaîne de caractères.',
            'dateTot.required' => 'La date de début est obligatoire.',
            'dateTot.date' => 'La date de début doit être une date valide.',
            'dateTot.before_or_equal' => 'La date de début doit être inférieure ou égale à la date de fin.',
            'dateTard.required' => 'La date de fin est obligatoire.',
            'dateTard.date' => 'La date de fin doit être une date valide.',
            'dateTard.after_or_equal' => 'La date de fin doit être supérieure ou égale à la date de début.',
        ]);

        DB::beginTransaction();
        try {

            $code_unique = $this->referenceService->generate();

            // Étape 4 : Créer un appel d'offre et gérer les transactions
            $appelOffre = $this->createAppelOffre($validatedData, $userId, $code_unique);
            // Étape 5 : Gérer les notifications des utilisateurs
            $this->notifyUsers($appelOffre);

            $difference = $this->selectedOption == 'Delivery' ? 'appelOffreD' : 'appelOffreR';
            $AppelOffreGrouper_id = 'id_appeloffre';
            $id = $appelOffre->id;

            $this->startCountdown($code_unique, $difference, $AppelOffreGrouper_id, $id);

            // Étape 6 : Réinitialiser le formulaire
            $this->reset([
                'quantité',
                'localite',
                'selectedOption',
                'dateTot',
                'dateTard',
                'timeStart',
                'timeEnd',
                'dayPeriod',
                'dayPeriodFin',
            ]);

            // Confirmer la transaction
            DB::commit();

            $this->dispatch('formSubmitted', "Demande d'appel d'offre effectuée avec succès.");
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la validation : ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.appaeloffre');
    }
}
