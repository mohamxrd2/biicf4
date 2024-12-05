<?php

namespace App\Livewire;

use App\Models\Coi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DepositSend extends Component
{

    public function montantRecu()
    {
        $userWallet = Wallet::where('user_id', $this->userDeposit->id)->first();

        $userCfa = Cfa::where('id_wallet', $userWallet->id)->first();

        if (!$userCfa) {
            session()->flash('error', 'Coi non trouvé pour le wallet.');
            return;
        }

        $userCfa->decrement('Solde', $this->amountDeposit);
        $userWallet->increment('balance', $this->amountDeposit);

        $referenceId = $this->generateIntegerReference();

        $this->createTransactionNew($this->userDeposit->id, $this->userDeposit->id, 'Envoie', 'CFA', $this->amountDeposit, $referenceId, 'Rechargement SOS');
        $this->createTransactionNew($this->userDeposit->id, $this->userDeposit->id, 'Réception', 'COC', $this->amountDeposit, $referenceId, 'Rechargement SOS');



        $this->notification->update(['reponse' => 'Recu']);

        session()->flash('success', 'L\'argent à été recu');
    }

    public function nonrecu()
    {
        $userWallet = Wallet::where('user_id', $this->userDeposit->id)->first();

        $userCfa = Cfa::where('id_wallet', $userWallet->id)->first();

        $userCfa->decrement('Solde', $this->amountDeposit);

        $investWallet = Wallet::where('user_id', Auth::id())->first();
        $investCoi = Coi::where('id_wallet', $investWallet->id)->first();

        $investCoi->increment('Solde', $this->amountDeposit);

        $referenceId = $this->generateIntegerReference();

        $this->createTransactionNew($this->userDeposit->id, Auth::id(), 'Envoie', 'CFA', $this->amountDeposit, $referenceId, 'Rechargement SOS');
        $this->createTransactionNew($this->userDeposit->id, Auth::id(), 'Réception', 'COI', $this->amountDeposit, $referenceId, 'Rechargement SOS');



        if (!$userCfa) {
            session()->flash('error', 'Coi non trouvé pour le wallet.');
            return;
        }
        $this->notification->update(['reponse' => 'Non recu']);

        session()->flash('success', 'L\'argent à n\'a pas été recu');
    }

    public function render()
    {
        return view('livewire.deposit-send');
    }
}
