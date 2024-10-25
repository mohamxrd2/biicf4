<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TransfertArgent extends Component
{
    public $search = '';
    public $users = [];
    public $user_id;
    public $amount;

    // Méthode appelée lors de la mise à jour de la recherche
    public function mount()
    {
        $this->users = User::all();
    }
    public function updatedSearch()
    {
        $this->users = User::where('username', 'like', '%' . $this->search . '%')->get();
    }

    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;
        $this->users = [];
    }

    public function submit()
    {


        $this->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ], [
            'user_id.required' => 'Veuillez sélectionner un utilisateur.',
            'amount.required' => 'Veuillez entrer un montant.',
            'amount.numeric' => 'Le montant doit être numérique.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
        ]);

        $senderId = Auth::id();
        $receiver = User::find($this->user_id);

        if (!$receiver) {
            Log::error('Utilisateur spécifié introuvable.', ['user_id' => $this->user_id]);
            return $this->handleError('L\'utilisateur spécifié n\'existe pas.');
        }

        $senderWallet = Wallet::where('user_id', $senderId)->first();
        $receiverWallet = Wallet::where('user_id', $receiver->id)->first();

        if (!$senderWallet || !$receiverWallet) {
            Log::error('Erreur lors de la récupération des portefeuilles.', [
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id
            ]);
            return $this->handleError('Erreur lors de la récupération des portefeuilles.');
        }

        if ($senderWallet->balance < $this->amount) {
            Log::warning('Solde insuffisant.', [
                'sender_balance' => $senderWallet->balance,
                'requested_amount' => $this->amount
            ]);
            return $this->handleError('Solde insuffisant pour effectuer la recharge.');
        }

        try {
            // Effectuer la transaction
            $this->processTransaction($senderWallet, $receiverWallet);

            // Générer une référence pour la transaction
            $referenceId = $this->generateIntegerReference();

            // Enregistrer la transaction
            $this->createTransaction($senderId, $receiver->id, 'Envoie', $this->amount, $referenceId, 'Envoie d\'argent');
            $this->createTransaction($receiver->id, $senderId, 'Réception', $this->amount, $referenceId, 'Réception d\'argent');

            Log::info('Recharge réussie.', [
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'amount' => $this->amount,
                'reference_id' => $referenceId,
                'sender_balance' => $senderWallet->balance - $this->amount,
                'receiver_balance' => $receiverWallet->balance + $this->amount
            ]);

            $this->dispatch('formSubmitted', 'Transfert effectué avec succès.');
            // Reset des champs après soumission
            $this->reset();
        } catch (\Exception $e) {
            Log::error('Erreur lors du transfert.', [
                'exception' => $e->getMessage(),
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'amount' => $this->amount
            ]);
            return $this->handleError('Une erreur est survenue lors du transfert.');
        }
    }

    protected function processTransaction($senderWallet, $receiverWallet)
    {
        $senderWallet->decrement('balance', $this->amount);
        $receiverWallet->increment('balance', $this->amount);
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
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
        return view('livewire.transfert-argent');
    }
}
