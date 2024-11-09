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


    public $errorMessage;

    // Méthode appelée lors de la mise à jour de la recherche
    public function mount()
    {
        $this->users = User::all();

        $this->resetForm(); // Réinitialiser les champs du formulaire par défaut
    }
    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // Récupérer l'ID de l'utilisateur connecté
            $currentUserId = auth()->id();

            // Recherche des utilisateurs dont le nom d'utilisateur correspond à la saisie,
            // mais exclure l'utilisateur connecté
            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->where('id', '!=', $currentUserId) // Exclure l'utilisateur connecté
                ->get();

            Log::info('Search updated.', ['search' => $this->search]);
        } else {
            // Si la barre de recherche est vide, ne rien afficher
            $this->users = [];
        }
    }


    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;
        $this->users = [];
    }

    public function submit()
    {
        // Réinitialiser le message d'erreur au début de la soumission
        $this->errorMessage = '';

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
            $this->errorMessage = 'L\'utilisateur spécifié n\'existe pas.';
            return; // Arrêtez l'exécution de la méthode
        }

        $senderWallet = Wallet::where('user_id', $senderId)->first();
        $receiverWallet = Wallet::where('user_id', $receiver->id)->first();

        if (!$senderWallet || !$receiverWallet) {
            Log::error('Erreur lors de la récupération des portefeuilles.', [
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id
            ]);
            $this->errorMessage = 'Erreur lors de la récupération des portefeuilles.';
            return;
        }

        if ($senderWallet->balance < $this->amount) {
            Log::warning('Solde insuffisant.', [
                'sender_balance' => $senderWallet->balance,
                'requested_amount' => $this->amount
            ]);
            $this->errorMessage = 'Solde insuffisant pour effectuer la recharge.';
            return;
        }

        try {
            // Effectuer la transaction
            $this->processTransaction($senderWallet, $receiverWallet);

            // Générer une référence pour la transaction
            $referenceId = $this->generateIntegerReference();


            $this->createTransactionNew($senderId, $receiver->id, 'Réception', 'COC', $this->amount, $referenceId, 'Réception d\'argent');
            $this->createTransactionNew($senderId, $receiver->id, 'Envoie', 'COC', $this->amount, $referenceId, 'Envoie d\'argent');

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
            $this->resetForm(); // Réinitialiser les champs du formulaire par défaut
            return redirect()->to(request()->header('Referer'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du transfert.', [
                'exception' => $e->getMessage(),
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'amount' => $this->amount
            ]);
            $this->errorMessage = 'Une erreur est survenue lors du transfert.';
        }
    }
    public function resetForm()
    {
        $this->search = '';
        $this->user_id = '';
        $this->amount = '';
    }

    protected function processTransaction($senderWallet, $receiverWallet)
    {
        $senderWallet->decrement('balance', $this->amount);
        $receiverWallet->increment('balance', $this->amount);
    }


    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
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
