<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\AcceptRetrait;
use App\Notifications\RefusRetrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Retrait extends Component
{

    public function accepteRetrait()
    {
        $userWallet = Wallet::where('user_id', $this->demandeur->id)->first();
        if (!$userWallet) {
            Log::info('Processing userWallet: ' . $userWallet);
            return redirect()->back()->with('error', 'Portefeuille de l\'utilisateur introuvable.');
        }

        $psapWallet = Wallet::where('user_id', $this->psap)->first();
        if (!$psapWallet) {
            Log::info('Processing psapWallet: ' . $psapWallet);
            return redirect()->back()->with('error', 'Portefeuille du PSA introuvable.');
        }

        DB::beginTransaction();

        try {
            $this->notification->update(['reponse' => 'accepter']);

            $userWallet->decrement('balance', $this->amount);
            $psapWallet->increment('balance', $this->amount);

            // $this->createTransaction($this->demandeur->id, Auth::id(), 'Reception', $this->amount);
            // $this->createTransaction($this->demandeur->id,  Auth::id(), 'withdrawal', $this->amount);

            $referenceId = $this->generateIntegerReference();

            $this->createTransactionNew($this->demandeur->id, Auth::id(), 'Envoie', 'COC', $this->amountDeposit, $referenceId, 'Retrait via PSAP');
            $this->createTransactionNew($this->demandeur->id, Auth::id(), 'Réception', 'COC', $this->amountDeposit, $referenceId, 'Retrait via PSAP');

            DB::commit();

            $demandeur = User::find($this->demandeur->id);

            session()->flash('success', 'Le retrait a été accepté.');
            Notification::send($demandeur, new AcceptRetrait($this->notification->id));
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors du retrait.');
        }
    }

    public function refusRetrait()
    {

        $this->notification->update(['reponse' => 'accepter']);

        $demandeur = User::find($this->demandeur->id);

        session()->flash('error', 'Le retrait a été refusé.');
        Notification::send($demandeur, new RefusRetrait($this->notification->id));
    }

    public function render()
    {
        return view('livewire.retrait');
    }
}
