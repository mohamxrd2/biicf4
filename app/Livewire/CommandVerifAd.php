<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\ProduitService;
use App\Notifications\mainleve;
use App\Notifications\mainleveAd;
use App\Notifications\RefusAchat;
use App\Notifications\colisaccept;
use App\Notifications\mainlevefour;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class CommandVerifAd extends Component
{
    public $namefourlivr;
    public $notification;
    public $id;
    public $idProd;
    public $totalPrice;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);
    }

    public function mainleve()
    {
        $id_client = Auth::user()->id;
        Log::info('le id du client', ['id_client' => $id_client]);
        $user = User::find($id_client);

        $livreur = User::find($this->notification->data['livreur']);
        Log::info('le id du livreur', ['livreur' => $livreur]);

        $fournisseur = User::find($this->notification->data['fournisseur']);
        Log::info('le id du fournisseur', ['fournisseur' => $fournisseur]);

        // Déterminer le prix unitaire
        $prixUnitaire = $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'];
        Log::info('Prix unitaire déterminé', ['prixUnitaire' => $prixUnitaire]);

        $quantite = $this->notification->data['quantite'] ?? $this->notification->data['quantiteC'];

        // Calculer le prix total
        $this->totalPrice = (int) ($quantite * $prixUnitaire + ($this->notification->data['prixTrade'] ?? 0));
        Log::info('Prix total calculé', ['totalPrice' => $this->totalPrice]);

        $produit = ProduitService::find($this->notification->data['idProd']);


        // Vérifier si l'utilisateur est authentifié
        if (!$fournisseur) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }


        $userWallet = Wallet::where('user_id', $fournisseur->id)->first();


        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->notification->data['code_unique'],
            'fournisseur' =>  $this->notification->data['fournisseur'],
            'localité' => $this->localite ?? null,
            'quantite' => $this->notification->data['quantite'],
            'id_client' => $id_client,
            'livreur' => $livreur->id  ?? null,
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd']

        ];
        Log::info('data', ['data' => $data]);


        if ($this->notification->type_achat == 'reserv/take') {
            Notification::send($fournisseur, new colisaccept($data));

            $userWallet = Wallet::where('user_id', $fournisseur->id)->first();

            $requiedAmount = $this->totalPrice - $this->totalPrice * 0.1;

            if (!$userWallet) {
                Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $fournisseur->id]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);
            // Déduire le montant requis du portefeuille de l'utilisateur
            $userWallet->increment('balance',  $requiedAmount);
            Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

            ///$this->createTransaction($user->id, $fournisseur->id, 'Reception', $this->totalPrice);

            $reference_id = $this->generateIntegerReference();

            $this->createTransaction($user->id, $fournisseur->id, 'Réception',  $requiedAmount, $reference_id, 'Reception pour achat de ' . $produit->name, 'effectué', 'COC');

            Notification::send($user, new colisaccept($data));
        } else {
            Notification::send($livreur, new mainleveAd($data));

            Notification::send($fournisseur, new mainlevefour($data));
        }


        $this->notification->update(['reponse' => 'mainleve']);
    }

    public function refuseVerif()
    {
        $id_client = Auth::user()->id;
        Log::info('le id du client', ['id_client' => $id_client]);
        $user = User::find($id_client);
        $userWallet = Wallet::where('user_id', $id_client)->first();

        $fournisseur = User::find($this->notification->data['fournisseur']);
        Log::info('le id du fournisseur', ['fournisseur' => $fournisseur]);

        $livreur = User::find($this->notification->data['livreur']);
        Log::info('le id du livreur', ['livreur' => $livreur]);

        $produit = ProduitService::find($this->notification->data['idProd']);



        if (!$userWallet) {
            Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $id_client]);
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        if ($this->notification->type_achat == 'reserv/take') {


            // Notification::send($fournisseur, new colisaccept($data));

            $userWallet = Wallet::where('user_id', $id_client)->first();

            // $requiedAmount = $this->totalPrice - $this->totalPrice * 0.1;

            if (!$userWallet) {
                Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $id_client]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);
            // Déduire le montant requis du portefeuille de l'utilisateur
            $userWallet->increment('balance',  $this->totalPrice);
            Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

            // ///$this->createTransaction($user->id, $fournisseur->id, 'Reception', $this->totalPrice);

             $reference_id = $this->generateIntegerReference();

             $this->createTransaction($user->id, $fournisseur->id, 'Réception',  $this->totalPrice, $reference_id, 'Refus  d\'achat de ' . $produit->name , 'effectué', 'COC');

             Notification::send($fournisseur , new RefusAchat('Le produit à été refusé'));
        } else {
            Notification::send($livreur , new RefusAchat('Le produit à été refusé'));

            Notification::send($fournisseur , new RefusAchat('Le produit à été refusé'));
        }


        $this->notification->update(['reponse' => 'mainleveRefusé']);
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
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    public function render()
    {
        return view('livewire.command-verif-ad');
    }
}
