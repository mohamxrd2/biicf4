<?php

namespace App\Livewire;

use App\Models\Cfa;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class DepositSend extends Component
{
    public $notification;
    public $userDeposit;
    public $amountDeposit;
    public $roiDeposit;

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->roiDeposit = $this->notification->data['roi'];
        $this->amountDeposit = $this->notification->data['amount'];
        $this->userDeposit = User::findOrFail($this->notification->data['user_id']);
    }

    public function montantRecu()
    {
        $userWallet = Wallet::where('user_id', $this->userDeposit->id)->firstOrFail();
        $userCfa = Cfa::where('id_wallet', $userWallet->id)->first();

        if (!$userCfa) {
            session()->flash('error', 'Coi non trouvé pour le wallet.');
            return;
        }

        $userCfa->decrement('Solde', $this->amountDeposit);
        $userWallet->increment('balance', $this->amountDeposit);

        $referenceId = $this->generateIntegerReference();

       
        $this->createTransaction(
            Auth::id(),
            $this->userDeposit->id,
            'Réception',
            $this->amountDeposit,
            $referenceId,
            'Rechargement SOS',
            'Effectué',
            'COC'
        );
        $this->createTransaction(
            $this->userDeposit->id,
            $this->userDeposit->id,
            'Envoie',
            $this->amountDeposit,
            $referenceId,
            'Rechargement SOS',
            'Effectué',
            'CFA'
        );

        $this->notification->update(['reponse' => 'Recu']);
        session()->flash('success', 'L\'argent a été reçu.');
    }

    public function nonrecu()
    {
        $userWallet = Wallet::where('user_id', $this->userDeposit->id)->firstOrFail();
        $userCfa = Cfa::where('id_wallet', $userWallet->id)->firstOrFail();

        $userCfa->decrement('Solde', $this->amountDeposit);
        $userWallet->increment('balance', $this->amountDeposit);

        $investWallet = Wallet::where('user_id', Auth::id())->firstOrFail();
        $investCoi = Cfa::where('id_wallet', $investWallet->id)->firstOrFail();

        $investCoi->increment('Solde', $this->amountDeposit);

        $referenceId = $this->generateIntegerReference();

        $this->createTransaction(
            $this->userDeposit->id,
            Auth::id(),
            'Envoie',
            $this->amountDeposit,
            $referenceId,
            'Rechargement SOS',
            'Effectué',
            'CFA'
        );

        $this->createTransaction(
            $this->userDeposit->id,
            Auth::id(),
            'Réception',
            $this->amountDeposit,
            $referenceId,
            'Rechargement SOS',
            'Effectué',
            'COI'
        );

        $this->notification->update(['reponse' => 'Non recu']);
        session()->flash('success', 'L\'argent n\'a pas été reçu.');
    }

    protected function createTransaction(
        int $senderId,
        int $receiverId,
        string $type,
        float $amount,
        int $referenceId,
        string $description,
        string $status,
        string $typeCompte
    ): void {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $referenceId;
        $transaction->description = $description;
        $transaction->type_compte = $typeCompte;
        $transaction->status = $status;
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        return (int) now()->getTimestamp() * 1000 + now()->micro;
    }

    public function render()
    {
        return view('livewire.deposit-send');
    }
}
