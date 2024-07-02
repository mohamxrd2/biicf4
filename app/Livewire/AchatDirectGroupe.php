<?php

namespace App\Livewire;

use App\Events\MyEvent;
use Livewire\Component;
use App\Models\ProduitService;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\AchatDirect as AchatDirectModel;
use App\Models\AchatGrouper;
use App\Models\Consommation;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AchatBiicf;
use App\Notifications\AchatGroupBiicf;
use Carbon\Carbon;


class AchatDirectGroupe extends Component
{
    public $id;
    public $produit;
    public $userId;
    public $userWallet;
    public $idsProprietaires;
    public $nombreProprietaires;
    public $nomFournisseur;
    public $nomFournisseurCount;
    public $nbreAchatGroup = "";
    public $datePlusAncienne;
    public $sommeQuantite;
    public $userSenders;
    public $montants;
    //
    public $quantité = "";
    public $localite = "";
    public $specificite = "";
    public $userTrader;
    public $nameProd;
    public $userSender;
    public $message = "Un utilisateur veut acheter ce produit";
    public $photoProd;
    public $idProd;
    public $prix;

    protected $rules = [
        'nameProd' => 'required|string',
        'quantité' => 'required|integer',
        'prix' => 'required|numeric',
        'localite' => 'required|string|max:255',
        'userTrader' => 'required|exists:users,id',
        'userSender' => 'required|exists:users,id',
        'photoProd' => 'required|string',
        'idProd' => 'required|exists:produit_services,id',
    ];

    public function mount($id)
    {
        try {
            $this->id = $id;
            $this->produit = ProduitService::findOrFail($id);
            $this->userId = Auth::guard('web')->id();
            $this->nameProd = $this->produit->name;
            $this->userSender = $this->userId;
            $this->userTrader = $this->produit->user->id;
            $this->photoProd = $this->produit->photoProd1;
            $this->idProd = $this->produit->id;
            $this->prix = $this->produit->prix;

            // Récupérer le portefeuille de l'utilisateur
            $this->userWallet = Wallet::where('user_id', $this->userId)->first();

            // Récupérer les IDs des propriétaires des consommations similaires
            $this->idsProprietaires = Consommation::where('name', $this->produit->name)
                ->where('id_user', '!=', $this->userId)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();
            // Compter le nombre d'IDs distincts
            $this->nombreProprietaires = count($this->idsProprietaires);

            // Récupérer les fournisseurs pour ce produit
            $this->nomFournisseur = ProduitService::where('name', $this->produit->name)
                ->where('user_id', '!=', $this->userId)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('user_id')
                ->toArray();
            $this->nomFournisseurCount = count($this->nomFournisseur);

            // Récupérer le nombre d'achats groupés distincts pour ce produit
            $this->nbreAchatGroup = AchatGrouper::where('idProd', $this->produit->id)
                ->distinct('userSender')
                ->count('userSender');
            // Récupérer la date la plus ancienne parmi les achats groupés pour ce produit
            $this->datePlusAncienne = AchatGrouper::where('idProd', $this->produit->id)->min('created_at');
            $tempsEcoule = $this->datePlusAncienne ? Carbon::parse($this->datePlusAncienne)->addMinutes(1) : null;

            // Vérifier si le temps est écoulé
            $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

            // Récupérer les autres informations nécessaires
            $this->sommeQuantite = AchatGrouper::where('idProd', $this->produit->id)->sum('quantité');
            $this->montants = AchatGrouper::where('idProd', $this->produit->id)->sum('montantTotal');
            $this->userSenders = AchatGrouper::where('idProd', $this->produit->id)
                ->distinct('userSender')
                ->pluck('userSender')
                ->toArray();
            // Vérifier si une notification a déjà été envoyée pour ce produit
            $notificationExists = NotificationLog::where('idProd', $this->produit->id)->exists();

            if ($isTempsEcoule && !$notificationExists && $this->nbreAchatGroup) {
                // Préparer le tableau de données pour la notification
                $notificationData = [
                    'nameProd' => $this->produit->name,
                    'quantité' => $this->sommeQuantite,
                    'montantTotal' => $this->montants,
                    'userTrader' => $this->produit->user->id,
                    'photoProd' => $this->produit->photoProd1,
                    'idProd' => $this->produit->id,
                    'userSender' => $this->userSenders
                ];

                // Envoyer la notification
                Notification::send($this->produit->user, new AchatGroupBiicf($notificationData));

                // Enregistrer la notification dans la table NotificationLog
                NotificationLog::create(['idProd' => $this->produit->id]);

                // Supprimer toutes les lignes dans AchatGrouper pour ce produit
                AchatGrouper::where('idProd', $this->produit->id)->delete();
            }
        } catch (\Exception $e) {
            // Gérer les exceptions et rediriger avec un message d'erreur
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function AchatDirectForm()
    {
        $validated = $this->validate();

        $userId = Auth::id();

        $montanTotal = $validated['quantité'] * $validated['prix'];

        if (!$userId) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id', $userId)->first();

        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        $requiredAmount = $montanTotal;

        if ($userWallet->balance < $requiredAmount) {
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        try {
            $achat = AchatDirectModel::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montanTotal,
                'localite' => $validated['localite'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $this->specificite,
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
            ]);

            $userWallet->decrement('balance', $requiredAmount);

            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $montanTotal;
            $transaction->save();

            $owner = User::find($validated['userTrader']);
            Notification::send($owner, new AchatBiicf($achat));

            $user = User::find($userId);
            event(new MyEvent($user));

            $this->reset(['quantité', 'localite', 'specificite']);
            session()->flash('success', 'Achat passé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function AchatGroupeForm()
    {
        $validated = $this->validate();

        $userId = Auth::id();

        $montanTotal = $validated['quantité'] * $validated['prix'];

        if (!$userId) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id', $userId)->first();

        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        $requiredAmount = $montanTotal;

        if ($userWallet->balance < $requiredAmount) {
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        try {
            $achat = AchatGrouper::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montanTotal,
                'localite' => $validated['localite'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $this->specificite,
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
            ]);

            // Déduire le montant du solde de l'utilisateur
            $userWallet->decrement('balance', $requiredAmount);

            // Enregistrer la transaction pour l'utilisateur connecté
            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $montanTotal;
            $transaction->save();

            $this->reset(['quantité', 'localite', 'specificite']);
            session()->flash('success', 'Achat passé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.achat-direct-groupe', [
            'id' => $this->id,
            'produit' => $this->produit,
            'userId' => $this->userId, // Passer l'ID de l'utilisateur connecté à la vue
            'userWallet' => $this->userWallet,
            'nbreAchatGroup' => $this->nbreAchatGroup,
            'datePlusAncienne' => $this->datePlusAncienne,
            'sommeQuantite' => $this->sommeQuantite,
            'montants' => $this->montants,
            'userSenders' => $this->userSenders,
            'idsProprietaires' => $this->idsProprietaires,
            'nombreProprietaires' => $this->nombreProprietaires,
            'nomFournisseur' => $this->nomFournisseur,
            'nomFournisseurCount' => $this->nomFournisseurCount,
        ]);
    }
}
