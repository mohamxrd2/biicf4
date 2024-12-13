<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AppelOffreGrouper;
use App\Models\AppelOffreUser;
use App\Models\Consommation;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\AOGrouper;
use App\Notifications\AppelOffre;
use App\Notifications\Confirmation;
use App\Services\RecuperationTimer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Str;

class Appaeloffre extends Component
{

    public $wallet;
    public $lowestPricedProduct;
    public $distinctCondProds;
    public $type;
    public $prodUsers = [];
    public $distinctquatiteMax;
    public $distinctquatiteMin;
    public $name;
    public $reference;
    public $distinctSpecifications = [];
    public $appliedZoneValue;
    public $quantité;
    public $localite;
    public $selectedOption;
    public $dateTot;
    public $dateTard;
    public $timeStart;
    public $timeEnd;
    public $dayPeriod;
    public $dayPeriodFin;
    public $id;
    public $loading = false;
    public $time;
    public $error;
    public $timestamp;

    protected $recuperationTimer;
    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
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
        $this->time = $this->recuperationTimer->getTime();
        $this->error = $this->recuperationTimer->error;
        // Convertir en secondes
        $seconds = intval($this->time / 1000);
        // Créer un objet Carbon pour le timestamp
        $this->timestamp = \Carbon\Carbon::createFromTimestamp($seconds);

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

        $this->id = ProduitService::where('reference', $reference)
            ->first();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->reference = '';
        $this->quantité = null;
        $this->localite = '';
        $this->selectedOption = null; // Réinitialisé à la valeur par défaut
        $this->dateTot = null;
        $this->dateTard = null;
        $this->timeStart = null;
        $this->timeEnd = null;
        $this->dayPeriod = null;
        $this->dayPeriodFin = null;
        $this->prodUsers = [];
        $this->distinctSpecifications = [];
    }



    public function submitEnvoie()
    {
        $this->loading = true;
        $userId = Auth::id();

        // Validation des données du formulaire
        $validatedData = $this->validate([
            'quantité' => 'required|integer|min:1',
            'localite' => 'required|string|max:255',
            'selectedOption' => 'required|string',
            'dateTot' => 'required|date|before_or_equal:dateTard',
            'dateTard' => 'required|date|after_or_equal:dateTot',
            'timeStart' => 'nullable|date_format:H:i',
            'timeEnd' => 'nullable|date_format:H:i',
            'dayPeriod' => 'nullable|string|max:255',
            'dayPeriodFin' => 'nullable|string|max:255',
        ]);

        // Calculer le coût total
        $totalCost = $validatedData['quantité'] * $this->lowestPricedProduct;

        // Vérification du solde avant décrémentation
        if ($this->wallet->balance < $totalCost) {
            $this->dispatch('formSubmitted', "Demande d'appel d'offre effectuée avec succès.");

            session()->flash('success', "Solde insuffisant pour effectuer cette transaction.");
            return;
        }

        // Gestion de la transaction avec la base de données
        DB::beginTransaction();
        try {
            // Décrémenter le solde du portefeuille
            $this->wallet->decrement('balance', $totalCost);

            // Insérer dans la table `appel_offres`
            $appelOffre = AppelOffreUser::create([
                'product_name' => $this->name,
                'quantity' => $validatedData['quantité'],
                'payment' => 'comptant',
                'livraison' => $validatedData['selectedOption'],
                'date_tot' => $validatedData['dateTot'],
                'date_tard' => $validatedData['dateTard'],
                'time_start' => $validatedData['timeStart'],
                'time_end' => $validatedData['timeEnd'],
                'day_period' => $validatedData['dayPeriod'],
                'day_periodFin' => $validatedData['dayPeriodFin'],
                'specification' => $this->distinctSpecifications,
                'reference' => $this->reference,
                'localite' => $validatedData['localite'],
                'id_prod' => $this->id,
                'code_unique' => $this->generateUniqueReference(),
                'lowestPricedProduct' => $this->lowestPricedProduct,
                'prodUsers' => json_encode($this->prodUsers),
                'image' => $validatedData['image'] ?? null,
                'id_sender' => $userId,
            ]);

            // Créer la transaction
            $this->createTransaction(
                $userId,
                $userId,
                'Gele',
                $totalCost,
                $this->generateIntegerReference(),
                'Gele Pour Achat de ' . $this->name,
                'effectué',
                'COC'
            );

            // Mettre à jour la table de gelement de fond
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $totalCost,
                'reference_id' => $this->generateUniqueReference(),
            ]);

            // Envoyer des notifications aux utilisateurs cibles
            foreach ($this->prodUsers as $prodUser) {
                $owner = User::find($prodUser);

                if ($owner) {
                    $data = [
                        'id_appelOffre' => $appelOffre->id,
                        'code_unique' => $appelOffre->code_unique,
                        'difference' => 'single',
                    ];

                    Notification::send($owner, new AppelOffre($data));

                    // Mettre à jour la notification
                    $notification = $owner->notifications()
                        ->where('type', AppelOffre::class)
                        ->latest()
                        ->first();

                    if ($notification) {
                        $notification->update(['type_achat' => $validatedData['selectedOption']]);
                    }

                    // Déclencher un événement
                    event(new NotificationSent($owner));
                }
            }

            // Réinitialiser le formulaire après succès
            $this->resetForm();
            $this->dispatch('formSubmitted', "Demande d'appel d'offre effectuée avec succès.");

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la validation : ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status,  string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->type_compte = $type_compte;
        $transaction->status = $status;
        $transaction->save();
    }

    public function submitGroupe()
    {
        Log::info('Début du processus de création de l\'appel d\'offre groupé.');

        // Vérifier si une zone économique est sélectionnée
        if (!$this->appliedZoneValue) {
            Log::warning('Aucune zone économique sélectionnée.');
            session()->flash('error', 'Veuillez sélectionner une zone économique pour pouvoir vous grouper.');
            return;
        }

        $this->loading = true;
        $userId = Auth::guard('web')->id(); // ID de l'utilisateur connecté
        Log::info('Utilisateur connecté.', ['user_id' => $userId]);

        DB::beginTransaction();

        try {
            // Validation des données
            $validatedData = $this->validate([
                'name' => 'required|string|max:255',
                'quantité' => 'required|integer|min:1',
                'selectedOption' => 'required|string|max:255',
                'dateTot' => 'required|date|before_or_equal:dateTard',
                'dateTard' => 'required|date|after_or_equal:dateTot',
                'distinctSpecifications' => 'required|string|max:500',
                'localite' => 'required|string|max:255',
                'appliedZoneValue' => 'required|string|max:255',
                'prodUsers' => 'required|array|min:1',
            ]);
            Log::info('Données validées.', ['validated_data' => $validatedData]);

            // Générer un code unique une seule fois
            $codeUnique = $this->generateUniqueReference();

            // Création de l'appel d'offre groupé
            $offre = AppelOffreGrouper::create([
                'lowestPricedProduct' => $this->lowestPricedProduct,
                'productName' => $validatedData['name'],
                'quantity' => $validatedData['quantité'],
                'payment' => 'comptant',
                'Livraison' => $validatedData['selectedOption'],
                'dateTot' => $validatedData['dateTot'],
                'dateTard' => $validatedData['dateTard'],
                'specificity' => $validatedData['distinctSpecifications'],
                'localite' => $validatedData['localite'],
                'image' => $validatedData['image'] ?? null,
                'prodUsers' => json_encode($validatedData['prodUsers']),
                'codeunique' => $codeUnique,
                'reference' => $this->reference,
                'user_id' => $userId,
                'started_at' => $this->timestamp,
            ]);
            Log::info('Appel d\'offre créé.', ['offre_id' => $offre->id]);

            // Enregistrer la quantité utilisateur
            userquantites::create([
                'user_id' => $userId,
                'localite' => $validatedData['localite'],
                'quantite' => $validatedData['quantité'],
                'code_unique' => $codeUnique,
            ]);
            Log::info('Quantité utilisateur enregistrée.');

            // Calcul du coût total
            $totalCost = $validatedData['quantité'] * $this->lowestPricedProduct;
            Log::info('Coût total calculé.', ['total_cost' => $totalCost]);

            // Vérifier et décrémenter le solde du portefeuille
            if ($this->wallet->balance < $totalCost) {
                Log::error('Solde insuffisant.', ['balance' => $this->wallet->balance, 'total_cost' => $totalCost]);
                throw new Exception("Votre solde est insuffisant pour effectuer cette transaction.");
            }

            $this->wallet->decrement('balance', $totalCost);
            Log::info('Solde du portefeuille décrémenté.', ['new_balance' => $this->wallet->balance]);

            // Enregistrer la transaction
            $this->createTransaction(
                $userId,
                $userId,
                'Gele',
                $totalCost,
                $this->generateIntegerReference(),
                'Gele pour groupage de ' . $validatedData['name'],
                'effectué',
                'COC'
            );
            Log::info('Transaction enregistrée.');

            // Enregistrer le gel des fonds
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $totalCost,
                'reference_id' => $codeUnique,
            ]);
            Log::info('Gel des fonds enregistré.');

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
                'code_unique' => $this->generateUniqueReference(),
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

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
    public function render()
    {
        return view('livewire.appaeloffre');
    }
}
