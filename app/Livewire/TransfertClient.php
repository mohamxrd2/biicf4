<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TransfertClient extends Component
{
    public $search = '';
    public $user_id;
    public $amount;



    public $users = [];

    public $selectedUsername = '';

    public function mount()
    {
        $this->users = [];
        Log::info('TransfertClient component mounted.');
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 2) { // Recherche après avoir tapé 3 caractères ou plus
            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->orWhere('name', 'like', '%' . $this->search . '%')
                ->limit(5)
                ->get();
        } else {
            $this->users = [];
        }
    }

    public function selectUser($id, $username)
    {
        $this->user_id = $id;
        $this->selectedUsername = $username; // Mettre à jour l'input avec le nom d'utilisateur sélectionné
        $this->search = $username; // Afficher le nom dans l'input de recherche
        $this->users = []; // Masquer la liste des résultats après la sélection
    }


    public function recharge()
    {
        Log::info('Tentative de recharge initiée.', [
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'sender_id' => Auth::id()
        ]);
    
        $validatedData = $this->validate([
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
            $referenceId = $this->generateReferenceId();
    
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
    
            session()->flash('success', 'Transfert effectué avec succès.');
            $this->reset(['search', 'amount', 'user_id']);
           
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

    protected function createTransaction($senderId, $receiverId, $type, $amount, $referenceId, $description)
    {
        Transaction::create([
            'sender_user_id' => $senderId,
            'receiver_user_id' => $receiverId,
            'type' => $type,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'description' => $description,
            'status' => 'effectué',
        ]);
    }

    protected function generateReferenceId()
    {
        return now()->getTimestamp() * 1000 + now()->micro;
    }

    protected function handleError($message)
    {
        Log::error($message);
        session()->flash('error', $message);
        return redirect()->refresh();
    }

    public function render()
    {
        return view('livewire.transfert-client', [
            'users' => $this->users,
        ]);
    }
}
