<?php

namespace App\Livewire;

use App\Models\Deposit;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;

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
        $this->userName = $this->deposit && $this->deposit->user 
            ? $this->deposit->user->name 
            : 'Utilisateur inconnu';
    }

    public function acceptDeposit()
    {
        // Vérification préalable de la notification
        if (!$this->deposit) {
            return $this->handleError('Notification introuvable.');
        }

        // Récupérer les informations nécessaires
        $amount = $this->deposit->montant;
        $userId = $this->deposit->user_id;
        $adminId = auth()->guard('admin')->id();

        if (!$adminId) {
            return $this->handleError('Administrateur introuvable.');
        }

        // Récupérer les portefeuilles utilisateur et administrateur
        $userWallet = Wallet::firstWhere('user_id', $userId);
        $adminWallet = Wallet::firstWhere('admin_id', $adminId);

        if (!$userWallet) {
            return $this->handleError('Portefeuille utilisateur introuvable.');
        }

        if (!$adminWallet) {
            return $this->handleError('Portefeuille administrateur introuvable.');
        }

        // Vérifier le solde du portefeuille administrateur
        if ($adminWallet->balance < $amount) {
            return $this->handleError('Le portefeuille administrateur ne dispose pas d\'assez de fonds.');
        }

        // Mettre à jour les soldes des portefeuilles
        $userWallet->balance += $amount;
        $adminWallet->balance -= $amount;

        // Enregistrer les modifications
        if (!$userWallet->save() || !$adminWallet->save()) {
            return $this->handleError('Erreur lors de la mise à jour des portefeuilles.');
        }

        // Générer une référence pour la transaction
        $referenceId = $this->generateIntegerReference();

        // Créer les transactions correspondantes
        $this->createTransaction(
            $adminId,
            $userId,
            'Réception',
            $amount,
            $referenceId,
            'Rechargement par virement bancaire',
            'Effectué',
            'COC',
        );

        $this->createTransaction(
            $adminId,
            $userId,
            'Envoie',
            $amount,
            $referenceId,
            'Rechargement par virement bancaire',
            'Effectué',
            'Compte virtuel'
        );

        // Mettre à jour l'état du dépôt
        $this->deposit->update(['statut' => 'Accepté']);

        // Message de succès
        session()->flash('message', 'Dépôt accepté avec succès. Le montant a été crédité au portefeuille de l\'utilisateur.');
    }

    public function rejectDeposit()
    {
        // Vérification de l'existence de la notification
        if (!$this->deposit) {
            return $this->handleError('Notification introuvable.');
        }

        // Mettre à jour le statut de la notification
        $this->deposit->update(['statut' => 'Refusé']);

        // Message de succès
        session()->flash('message', 'Dépôt rejeté avec succès.');
    }

    private function handleError(string $message)
    {
        session()->flash('error', $message);
        return;
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status,  string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_admin_id = $senderId;
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
        return view('livewire.detail-deposit');
    }
}
