<?php

namespace App\Livewire;

use Exception;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Notifications\RefusRetrait;
use Illuminate\Support\Facades\Log;
use App\Notifications\AcceptRetrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class Retrait extends Component
{
    public $demandeur;
    public $psap;
    public $amount;
    public $notification;

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->demandeur = User::find($this->notification->data['userId']);
        $this->psap = User::find($this->notification->data['psap']);
        $this->amount = $this->notification->data['amount'];
    }

    public function accepteRetrait()
    {
        $userWallet = Wallet::where('user_id', $this->demandeur->id)->first();
        if (!$userWallet) {
            session()->flash('error', 'Portefeuille de l\'utilisateur introuvable.');
            return;
        }

        $psapWallet = Wallet::where('user_id', Auth::id())->first();
        if (!$psapWallet) {
            session()->flash('error', 'Portefeuille du PSA introuvable.');
            return;
        }

        DB::beginTransaction();

        try {
            

            // Mettre à jour les soldes des portefeuilles
            $userWallet->decrement('balance', $this->amount);
            $psapWallet->increment('balance', $this->amount);

            // Générer une référence unique
            $referenceId = $this->generateIntegerReference();

            // Créer les transactions associées
            $this->createTransactionNew($this->demandeur->id, Auth::id(), 'Envoie', 'COC', $this->amount, $referenceId, 'Retrait via PSAP');
            $this->createTransactionNew($this->demandeur->id, Auth::id(), 'Réception', 'COC', $this->amount, $referenceId, 'Retrait via PSAP');

            DB::commit();

            // Envoyer une notification au demandeur
            // Notification::send($this->demandeur, new AcceptRetrait($this->notification->id));

            session()->flash('success', 'Le retrait a été accepté.');

            // Mettre à jour la notification
            $this->notification->update(['reponse' => 'accepter']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'acceptation du retrait : ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors du retrait.');
        }
    }

    public function refusRetrait()
    {
        try {
           

            // Envoyer une notification de refus au demandeur
            Notification::send($this->demandeur, new RefusRetrait($this->notification->id));

            session()->flash('success', 'Le retrait a été refusé.');

             // Mettre à jour la notification
             $this->notification->update(['reponse' => 'refuser']);
        } catch (Exception $e) {
            Log::error('Erreur lors du refus du retrait : ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors du refus du retrait.');
        }
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
        return (int) (now()->timestamp * 1000 + now()->micro);
    }

    public function render()
    {
        return view('livewire.retrait');
    }
}
