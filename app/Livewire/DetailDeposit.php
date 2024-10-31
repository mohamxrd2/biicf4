<?php

namespace App\Livewire;

use App\Models\Deposit;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\NotificationEd;
use App\Models\User; // Importez le modèle User
use Illuminate\Notifications\DatabaseNotification;
use App\Models\Wallet; // Assurez-vous d'importer le modèle Wallet

class DetailDeposit extends Component
{
    public $id; // ID de la notification
    public $deposit; // Détails du dépôt
    public $userName; // Nom de l'utilisateur

    public function mount($id)
    {
        $this->id = $id;

        // Récupération du dépôt en fonction de l'ID
        $this->deposit = Deposit::with('user')->find($this->id);

        // Vérification de l'existence du dépôt et récupération du nom de l'utilisateur
        if ($this->deposit) {
            $this->userName = $this->deposit->user ? $this->deposit->user->name : 'Utilisateur inconnu';
        } else {
            $this->userName = 'Utilisateur inconnu';
        }
    }

    public function acceptDeposit()
    {
        // Vérifier si la notification existe
        if (!$this->deposit) {
            session()->flash('error', 'Notification introuvable.');
            return;
        }

        // Mettre à jour la réponse de la notification
        $this->deposit->statut = 'Accepté'; 
        $this->deposit->save();

        // Récupérer le montant et l'ID de l'utilisateur du dépôt
        $amount = $this->deposit->montant; // Assurez-vous que le nom du champ est correct
        $userId = $this->deposit->user_id;
        $adminId = auth()->guard('admin')->id();


        // Vérifier si l'administrateur existe
        if (!$adminId) {
            session()->flash('error', 'Administrateur introuvable.');
            return;
        }

        // Récupérer le portefeuille de l'utilisateur
        $userWallet = Wallet::where('user_id', $userId)->first();

        // Vérifier si le portefeuille existe
        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        // Ajouter le montant au solde du portefeuille
        $userWallet->balance += $amount;

        // Enregistrer le nouveau solde
        if ($userWallet->save()) {
            // Créer une transaction
            $referenceId = $this->generateIntegerReference();
            $this->createTransaction($adminId, $userId, 'Envoie', $amount, $referenceId, 'Rechargement par virement bancaire');

            // Message de succès
            session()->flash('message', 'Dépôt accepté avec succès et le montant a été crédité au portefeuille de l\'utilisateur.');
        } else {
            session()->flash('error', 'Erreur lors de la mise à jour du portefeuille.');
        }
    }

    public function rejectDeposit()
    {
        if (!$this->deposit) {
            session()->flash('error', 'Notification introuvable.');
            return;
        }

        // Mettre à jour la réponse de la notification
        $this->deposit->statut = 'Refusé';
        $this->deposit->save();
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_admin_id = $senderId; // Assurez-vous que cela correspond au bon modèle
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
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
        return view('livewire.detail-deposit');
    }
}
