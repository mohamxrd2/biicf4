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



    public function mount()
    {
        $this->users = User::all();
        Log::info('RechargeClient component mounted.');
    }

    public function updatedSearch()
    {
        $this->users = User::where('username', 'like', '%' . $this->search . '%')->get();
        Log::info('Search updated.', ['search' => $this->search]);
    }

    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;
        $this->users = [];
        Log::info('User selected.', ['user_id' => $userId, 'user_name' => $userName]);
    }

    public function recharge()
    {
        try {
            // Validate request data
            $validatedData = $this->validate(
                [
                    'user_id' => 'required',
                    'amount' => 'required|numeric',
                ],
                [
                    'user_id.required' => 'Veuillez sélectionner un agent.',
                    'amount.required' => 'Veuillez entrer le montant.',
                    'amount.numeric' => 'Le montant doit être numérique.',
                ]
            );
            Log::info('Recharge data validated.', $validatedData);

            $user = User::find($this->user_id);

            if (!$user) {
                Log::error('User not found.', ['user_id' => $this->user_id]);
                session()->flash('error', 'L\'utilisateur spécifié n\'existe pas.');
                return;
            }

            $UserId = Auth::id();
            Log::info('User ID.', ['user_id' => $UserId]);

            $userWallet = Wallet::where('user_id', $user->id)->first();
            $UserIdWallet = Wallet::where('user_id', $UserId)->first();

            if (!$userWallet || !$UserIdWallet) {
                Log::error('Wallet not found.', ['user_wallet' => $userWallet, 'userconnecte_wallet' => $UserIdWallet]);
                session()->flash('error', 'Erreur lors de la récupération des portefeuilles.');
                return;
            }

            if ($UserIdWallet->balance < $this->amount) {
                Log::error('Insufficient balance.', ['admin_wallet_balance' => $UserIdWallet->balance, 'amount' => $this->amount]);
                session()->flash('error', 'Solde insuffisant pour effectuer la recharge.');
                return;
            }

            // Effectuer la recharge
            $userWallet->increment('balance', $this->amount);
            $UserIdWallet->decrement('balance', $this->amount);

            Log::info('Balances updated.', [
                'user_wallet_balance' => $userWallet->balance,
                'admin_wallet_balance' => $UserIdWallet->balance,
                'amount' => $this->amount
            ]);

            // Enregistrer les transactions
            $transaction1 = new Transaction();
            $transaction1->sender_user_id = $UserId;
            $transaction1->receiver_user_id = $user->id;
            $transaction1->type = 'Reception';
            $transaction1->amount = $this->amount;
            $transaction1->save();

            $transaction2 = new Transaction();
            $transaction2->sender_user_id = $UserId;
            $transaction2->receiver_user_id = $user->id;
            $transaction2->type = 'Envoie';
            $transaction2->amount = $this->amount;
            $transaction2->save();

            Log::info('Transactions created.', [
                'transaction1' => $transaction1->id,
                'transaction2' => $transaction2->id
            ]);

            Log::info('Recharge successful.');

            // Notification de succès
            $this->dispatch('formSubmitted', 'transfert effectué avec succes');

            // Réinitialiser les valeurs
            $this->reset(['user_id', 'amount', 'search']);
            $this->dispatch('refreshComponent');
        } catch (\Exception $e) {
            // Log the exception with the error message
            Log::error('An error occurred during recharge.', ['error' => $e->getMessage()]);
            session()->flash('error', 'Une erreur est survenue lors du processus de recharge.');
        }
    }

    public function render()
    {
        return view('livewire.transfert-client', [
            'users' => $this->users,
        ]);
    }
}
