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
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class AchatDirectGroupe extends Component
{
    public $id;
    public $produit;
    public $userId;
    public $selectedOption;
    public $options = [
        'Achact avec livraison',
        'Take Away',
        'Reservation',
    ];
    //
    public $quantité = "";
    public $localite = "";
    public $specificite1 = false;
    public $specificite2 = false;
    public $specificite3 = false;
    public $userTrader;
    public $nameProd;
    public $userSender;
    public $message = "Un utilisateur veut acheter ce produit";
    public $photoProd;
    public $idProd;
    public $prix;

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
        $validated = $this->validate([
            'nameProd' => 'required|string',
            'quantité' => 'required|integer',
            'prix' => 'required|numeric',
            'selectedOption' => 'required|string',
            'localite' => 'required|string|max:255',
            'userTrader' => 'required|exists:users,id',
            'userSender' => 'required|exists:users,id',
            'photoProd' => 'required|string',
            'idProd' => 'required|exists:produit_services,id',
        ]);

        Log::info('Validation réussie.', $validated);

        $userId = Auth::id();
        $montantTotal = $validated['quantité'] * $validated['prix'];

        if (!$userId) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id', $userId)->first();

        if (!$userWallet) {
            Log::error('Portefeuille introuvable.', ['userId' => $userId]);
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        if ($userWallet->balance < $montantTotal) {
            Log::warning('Fonds insuffisants pour effectuer cet achat.', [
                'userId' => $userId,
                'requiredAmount' => $montantTotal,
                'walletBalance' => $userWallet->balance,
            ]);
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        try {
            // Créez un tableau pour stocker les spécifications sélectionnées
            $selectedSpecificites = [];

            if ($this->specificite1) {
                $selectedSpecificites[] = $this->produit->specification;
            }
            if ($this->specificite2) {
                $selectedSpecificites[] = $this->produit->specification2;
            }
            if ($this->specificite3) {
                $selectedSpecificites[] = $this->produit->specification3;
            }

            // Vérifiez que le tableau $selectedSpecificites n'est pas vide avant de l'utiliser
            $specificites = !empty($selectedSpecificites) ? implode(', ', $selectedSpecificites) : null;

            $achat = AchatDirectModel::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montantTotal,
                'localite' => $validated['localite'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $specificites,
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
            ]);

            $userWallet->decrement('balance', $montantTotal);

            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $montantTotal;
            $transaction->save();

            Log::info('Transaction enregistrée.', [
                'transactionId' => $transaction->id,
                'amount' => $montantTotal,
            ]);

            $owner = User::find($validated['userTrader']);
            $selectedOption = $this->selectedOption;
            Notification::send($owner, new AchatBiicf($achat));
            // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
            $notification = $owner->notifications()->where('type', AchatBiicf::class)->latest()->first();

            if ($notification) {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => $selectedOption]);
            }

            $user = User::find($userId);
            $this->reset(['quantité', 'localite', 'specificite1', 'specificite2', 'specificite3']);
            session()->flash('success', 'Achat passé avec succès.');
            $this->dispatch('sendNotification', $user);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
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
