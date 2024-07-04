<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Notification;
use App\Models\AchatDirect as AchatDirectModel;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Events\MyEvent;
use App\Models\AchatGrouper;
use App\Models\Consommation;
use App\Models\NotificationLog;
use App\Notifications\AchatBiicf;
use App\Notifications\AchatGroupBiicf;
use Carbon\Carbon;
use Livewire\Attributes\On;

class AchatDirectGroupe extends Component
{
    public $id;
    public $produit;
    public $userId;
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

    protected $listeners = ['sendNotification' => 'verifierEtEnvoyerNotification'];
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
        $this->id = $id;
        $this->produit = ProduitService::findOrFail($id);
        $this->userId = Auth::guard('web')->id();
        $this->nameProd = $this->produit->name;
        $this->userSender = $this->userId;
        $this->userTrader = $this->produit->user->id;
        $this->photoProd = $this->produit->photoProd1;
        $this->idProd = $this->produit->id;
        $this->prix = $this->produit->prix;
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

            $this->dispatch('start-timer');
            session()->flash('success', 'Achat passé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function startTimer()
    {
        // Rien à faire ici, le timer est géré en JavaScript côté client
    }

    #[On('sendNotification')]
    public function verifierEtEnvoyerNotification()
    {
        $produit = ProduitService::findOrFail($this->id);

        $datePlusAncienne = AchatGrouper::where('idProd', $produit->id)->min('created_at');
        $tempsEcoule = $datePlusAncienne ? Carbon::parse($datePlusAncienne)->addMinutes(1) : null;
        $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

        $nbreAchatGroup = AchatGrouper::where('idProd', $produit->id)
            ->distinct('userSender')
            ->count('userSender');

        $notificationExists = NotificationLog::where('idProd', $produit->id)->exists();

        if ($isTempsEcoule && !$notificationExists && $nbreAchatGroup) {
            $sommeQuantite = AchatGrouper::where('idProd', $produit->id)->sum('quantité');            $montants = AchatGrouper::where('idProd', $produit->id)->sum('montantTotal');
            $userSenders = AchatGrouper::where('idProd', $produit->id)
                ->distinct('userSender')
                ->pluck('userSender')
                ->toArray();

            $notificationData = [
                'nameProd' => $produit->name,
                'quantité' => $sommeQuantite,
                'montantTotal' => $montants,
                'userTrader' => $produit->user->id,
                'photoProd' => $produit->photoProd1,
                'idProd' => $produit->id,
                'userSender' => $userSenders
            ];

            Notification::send($produit->user, new AchatGroupBiicf($notificationData));

            NotificationLog::create(['idProd' => $produit->id]);
            AchatGrouper::where('idProd', $produit->id)->delete();
        }
    }
    public function render()
    {
        // Récupérer le produit ou échouer
        $produit = ProduitService::findOrFail($this->id);

        // Récupérer l'identifiant de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer le portefeuille de l'utilisateur
        $userWallet = Wallet::where('user_id', $userId)->first();

        // Récupérer les IDs des propriétaires des consommations similaires
        $idsProprietaires = Consommation::where('name', $produit->name)
            ->where('id_user', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // Compter le nombre d'IDs distincts
        $nombreProprietaires = count($idsProprietaires);

        // Récupérer les fournisseurs pour ce produit
        $nomFournisseur = ProduitService::where('name', $produit->name)
            ->where('user_id', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        $nomFournisseurCount = count($nomFournisseur);

        // Récupérer le nombre d'achats groupés distincts pour ce produit
        $nbreAchatGroup = AchatGrouper::where('idProd', $produit->id)
            ->distinct('userSender')
            ->count('userSender');

        // Récupérer la date la plus ancienne parmi les achats groupés pour ce produit
        $datePlusAncienne = AchatGrouper::where('idProd', $produit->id)->min('created_at');
        $tempsEcoule = $datePlusAncienne ? Carbon::parse($datePlusAncienne)->addMinutes(1) : null;


        // $this->verifierEtEnvoyerNotification();

        return view('livewire.achat-direct-groupe', compact(
            'produit',
            'userWallet',
            'userId',
            'nbreAchatGroup',
            'datePlusAncienne',
            'idsProprietaires',
            'nombreProprietaires',
            'nomFournisseur',
            'nomFournisseurCount',
        ));
    }
}
