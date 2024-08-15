<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class WithdrawalComponent extends Component
{

    public $amount;
    public $formVisible = false;

    public function showForm()
    {
        $this->formVisible = true;
    }
    public function initiateWithdrawal()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = Wallet::where('user_id', Auth::id())->first();

        if ($wallet && $wallet->balance >= $this->amount) {
            // Deduct amount from wallet
            $wallet->balance -= $this->amount;
            $wallet->save();

            // Create transaction
            Transaction::create([
                'sender_user_id' => Auth::id(),
                'receiver_user_id' => null, // No receiver for withdrawal
                'type' => 'withdrawal',
                'amount' => $this->amount,
            ]);

            session()->flash('message', 'Retrait effectué avec succès.');
            $this->formVisible = false;
            $this->amount = null;
        } else {
            session()->flash('error', 'Solde insuffisant pour effectuer ce retrait.');
        }
    }
    public function render()
    {
        return view('livewire.withdrawal-component');
    }
}
