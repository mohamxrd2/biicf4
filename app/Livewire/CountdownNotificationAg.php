<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\commandVerifag;
use App\Notifications\commandVerifAp;
use App\Notifications\mainleve;
use App\Notifications\mainlevefour;
use App\Notifications\VerifUser;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class CountdownNotificationAg extends Component
{
    public $notification;
    public $id;
    public $produitfat;
    public $userFour;
    public $totalPrice;
    public $user;



    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->userFour = User::find($this->notification->data['fournisseur'] ?? null);
        $this->user = Auth::id(); // Initialisation de $user avec l'utilisateur authentifié


        $this->produitfat = ($this->notification->type === 'App\Notifications\AppelOffreGrouperNotification'
            || $this->notification->type === 'App\Notifications\AppelOffreTerminer'
            || $this->notification->type === 'App\Notifications\AppelOffreTerminerGrouper'
            || $this->notification->type === 'App\Notifications\AppelOffre'
            || $this->notification->type === 'App\Notifications\OffreNotifGroup'
            || $this->notification->type === 'App\Notifications\NegosTerminer'
            || $this->notification->type === 'App\Notifications\OffreNegosNotif'
            || $this->notification->type === 'App\Notifications\OffreNegosDone'
            || $this->notification->type === 'App\Notifications\AOGrouper'
            ||  $this->notification->type === 'App\Notifications\OffreNotif'
            || $this->notification->type === 'App\Notifications\Retrait')
            ? null
            : (ProduitService::find($this->notification->data['idProd']) ?? $this->notification->data['produit_id'] ?? null);
    }

    public function valider()
    {
        // Déterminer le prix unitaire
        $prixUnitaire = $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'];
        Log::info('Prix unitaire déterminé', ['prixUnitaire' => $prixUnitaire]);

        $quantite = $this->notification->data['quantite'] ?? $this->notification->data['quantiteC'];

        // Calculer le prix total
        $this->totalPrice = (int) ($quantite * $prixUnitaire + ($this->notification->data['prixTrade'] ?? 0));
        Log::info('Prix total calculé', ['totalPrice' => $this->totalPrice]);

        // Vérifier si l'utilisateur est authentifié
        if (!$this->user) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userSender = User::find($this->user);

        if (!$userSender) {
            Log::error('Utilisateur non trouvé avec ID', ['userId' => $this->user]);
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }
        Log::info('Utilisateur trouvé', ['userSender' => $userSender]);

        $userWallet = Wallet::where('user_id', $userSender->id)->first();

        if (!$userWallet) {
            Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $userSender->id]);
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }
        Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);

        $requiredAmount = $this->totalPrice;

        if ($userWallet->balance < $requiredAmount) {
            Log::error('Fonds insuffisants pour l\'achat', ['balance' => $userWallet->balance, 'requiredAmount' => $requiredAmount]);
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        // Déduire le montant requis du portefeuille de l'utilisateur
        $userWallet->decrement('balance', $requiredAmount);
        Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

        $this->createTransaction($userSender->id, $userSender->id, 'Envoie', $requiredAmount);

        // Vérifiez si $this->userFour est défini
        if (!isset($this->userFour) || !$this->userFour) {
            Log::error('Livreur introuvable.');
            session()->flash('error', 'Livreur introuvable.');
            return;
        }
        Log::info('Vérification de userFour', ['userFour' => $this->userFour]);

        // Préparer les données de notification
        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' =>  $this->notification->data['code_unique'],
            'fournisseur' => $this->notification->data['fournisseur'],
            'localité' => $this->localite ?? null,
            'quantite' => $quantite,
            'livreur' => $this->notification->data['livreur'] ?? null, // Assurez-vous que $this->userFour est défini
            'prixTrade' => $this->notification->data['prixTrade'] ?? null,
            'prixProd' => $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'],
            'date_tot' => $this->notification->data['date_tot'],
            'date_tard' => $this->notification->data['date_tard'],
            'nameprod' => $this->notification->data['nameprod'],
            'specificite' => $this->notification->data['specificite'],
        ];

        $user = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->notification->data['code_unique'],
            'fournisseur' => $this->userFour->id ?? $this->notification->data['fournisseur'],
            'localité' => $this->localite ?? null,
            'quantite' => $quantite,
            'id_client' => Auth::id(),
            'prixProd' => $this->notification->data['prixProd'],
            'date_tot' => $this->notification->data['date_tot'],
            'date_tard' => $this->notification->data['date_tard'],
        ];

        $id_trader =  $this->notification->data['fournisseur'];
        $traderUser = User::find($id_trader);

        Log::info('Notification envoyée au userSender', ['userId' => $userSender->id, 'data' => $data]);


        // Envoi de la notification
        if ($this->notification->data['specificite'] === 'NOPRO') {

            $livreur = User::find($this->notification->data['livreur']);
            Log::info('le id du livreur', ['livreur' => $livreur]);

            $fournisseur = User::find($this->notification->data['fournisseur']);
            Log::info('le id du fournisseur', ['fournisseur' => $fournisseur]);
            $id_client = Auth::user()->id;
            Log::info('le id du client', ['id_client' => $id_client]);

            $donne = [
                'idProd' => $this->notification->data['idProd'],
                'code_unique' => $this->notification->data['code_unique'],
                'fournisseur' =>  $this->notification->data['fournisseur'],
                'localité' => $this->localite ?? null,
                'quantite' => $this->notification->data['quantiteC'],
                'id_client' => $id_client,
                'livreur' => $this->notification->data['livreur'],
                'prixTrade' => $this->notification->data['prixTrade'],
                'prixProd' => $this->notification->data['prixProd'],
                'date_tot' => $this->notification->data['date_tot'],
                'date_tard' => $this->notification->data['date_tard'],
            ];


            Notification::send($livreur, new mainleve($donne));

            Notification::send($fournisseur, new mainlevefour($donne));
        } else {
            Notification::send($userSender, new commandVerifag($data));

            // Utilisez && pour vérifier que les deux conditions sont vraies
            Notification::send($traderUser, new VerifUser($user));
        }



        // Mettre à jour la notification et valider
        $this->notification->update(['reponse' => 'valide']);
        Log::info('Notification mise à jour', ['notificationId' => $this->notification->id]);

        session()->flash('success', 'Validation effectuée avec succès.');
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
        return view('livewire.countdown-notification-ag');
    }
}
