<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\colisaccept;
use App\Notifications\mainleve;
use App\Notifications\mainleveAd;
use App\Notifications\mainlevefour;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

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

            if (!$userWallet) {
                Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $fournisseur->id]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);
            // Déduire le montant requis du portefeuille de l'utilisateur
            $userWallet->decrement('balance', $this->totalPrice);
            Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

            $this->createTransaction($user->id, $fournisseur->id, 'Reception', $this->totalPrice);

            Notification::send($user, new colisaccept($data));
        } else {
            Notification::send($livreur, new mainleveAd($data));

            Notification::send($fournisseur, new mainlevefour($data));
        }


        $this->notification->update(['reponse' => 'mainleve']);
    }


    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }

    public function render()
    {
        return view('livewire.command-verif-ad');
    }
}
