<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\Countdown;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\OffreNegosNotif;
use App\Notifications\RefusAchat;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use App\Services\TransactionService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class ConfirmationNotif extends Component
{
    public $notification;
    public $id;
    public $user;
    public $produit;
    public $userWallet;
    public $countdownId;
    public $isRunning;
    public $timeRemaining;
    public $isLoading = false;
    public $time;
    public $error;
    public $timestamp;
    protected $recuperationTimer;
    public function __construct()
    {

        $this->recuperationTimer = new RecuperationTimer();
    }
    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $id = Auth::id();
        $this->user = User::findOrFail($id);
        $this->produit = ProduitService::findOrFail($this->notification->data['idProd']);
        $this->userWallet = Wallet::where('user_id', $id)->first();
    }
    public function CommandeAccepter()
    {
        $transactionService = new TransactionService();
        $uniqueCode = $this->notification->data['code_unique'];
        $fournisseursFiltered = $this->notification->data['fourniCible'];
        if ($this->isLoading) {
            return; // Si l'action est déjà en cours, ne rien faire
        }

        $this->isLoading = true; // Marquer comme en cours

        try {
            // Vérifier si l'utilisateur a assez de fonds
            $prixTotal = $this->produit->prix * $this->notification->data['quantiteTotal'];
            $userBalance = $this->userWallet->balance;

            if ($userBalance < $prixTotal) {
                session()->flash('error', 'Fonds insuffisants pour effectuer cette commande.');
                return;
            }

            // Notifier les fournisseurs
            foreach ($fournisseursFiltered as $supplierId) {
                $supplier = User::find($supplierId);
                if ($supplier) {
                    Notification::send($supplier, new OffreNegosNotif([
                        'idProd' => $this->produit->id,
                        'produit_name' => $this->produit->name,
                        'quantite' => $this->notification->data['quantiteTotal'],
                        'code_unique' => $uniqueCode,
                    ]));
                    event(new NotificationSent($supplier));
                }
            }
            // Décrementer le solde disponible
            $this->userWallet->decrement('balance', $prixTotal);
            // Vérification et création de l'achat dans les transactions gelées
            gelement::create([
                'reference_id' => $uniqueCode,
                'id_wallet' => $this->userWallet->id,
                'amount' => $prixTotal,
            ]);

            // Créer la transaction
            $transactionService->createTransaction(
                Auth::id(),
                Auth::id(),
                'Gele',
                $prixTotal,
                $this->generateIntegerReference(),
                'Paiement pour achat.',
                'COC'
            );

            // Démarrer le compte à rebours
            $difference = 'offreGrouper';
            $this->startCountdown($uniqueCode, $difference);
            $this->notification->update(['reponse' => 'accepter']);

            session()->flash('success', 'Commande acceptée et fonds gelés avec succès.');
        } catch (\Exception $e) {
            // Gérer les erreurs et afficher un message à l'utilisateur
            Log::error("Erreur lors de la commande acceptée : " . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors du traitement de votre commande. Veuillez réessayer.');
        } finally {
            $this->isLoading = false; // Réinitialiser l'état après l'action
        }
    }

    public function startCountdown($code_unique, $difference)
    {
        $this->timeServer();
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
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();
        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }
    public function CommandeRefuser()
    {
        if ($this->isLoading) {
            return; // Si l'action est déjà en cours, ne rien faire
        }

        $this->isLoading = true; // Marquer comme en cours

        try {
            Notification::send(User::find($this->produit->user_id), new RefusAchat([
                'code_unique' => $this->notification->data['code_unique'],
                'id' => $this->produit->id,
                'title' => 'Commande Refusée',
                'description' => 'Votre commande a été refusée en raison de contraintes spécifiques du client.Merci de votre compréhension.',
            ]));
            $this->notification->update(['reponse' => 'refuser']);
        } catch (\Exception $e) {
            // Gérer les erreurs et afficher un message à l'utilisateur
            Log::error("Erreur lors de la commande acceptée : " . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors du traitement de votre commande. Veuillez réessayer.');
        } finally {
            $this->isLoading = false; // Réinitialiser l'état après l'action
        }
    }
    public function render()
    {
        return view('livewire.confirmation-notif');
    }
}
