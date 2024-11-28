<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RechargeClient extends Component
{
    public $search = '';
    public $user_id;
    public $amount;

    public $users = [];

    protected $rules = [
        'user_id' => 'required',
        'amount' => 'required|numeric',
    ];
    protected $messages = [
        'user_id.required' => 'Veuillez sélectionner un agent.',
        'amount.required' => 'Veuillez entrer le montant.',
        'amount.numeric' => 'Le montant doit être numérique.',
    ];

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

    public function recharge()
    {
        $this->validate();

        $user = User::find($this->user_id);

        if (!$user) {
            session()->flash('error', 'L\'utilisateur spécifié n\'existe pas.');
            return;
        }

        $adminId = Auth::guard('admin')->id();

        $userWallet = Wallet::where('user_id', $user->id)->first();
        $adminWallet = Wallet::where('admin_id', $adminId)->first();

        if (!$userWallet || !$adminWallet) {
            session()->flash('error', 'Erreur lors de la récupération des portefeuilles.');
            return;
        }

        if ($adminWallet->balance < $this->amount) {
            session()->flash('error', 'Solde insuffisant pour effectuer la recharge.');
            return;
        }

        $userWallet->increment('balance', $this->amount);
        $adminWallet->decrement('balance', $this->amount);

        // $transaction1 = new Transaction();
        // $transaction1->sender_admin_id = $adminId;
        // $transaction1->receiver_user_id = $user->id;
        // $transaction1->type = 'Reception';
        // $transaction1->amount = $this->amount;
        // $transaction1->save();

        // $transaction2 = new Transaction();
        // $transaction2->sender_admin_id = $adminId;
        // $transaction2->receiver_user_id = $user->id;
        // $transaction2->type = 'Envoie';
        // $transaction2->amount = $this->amount;
        // $transaction2->save();

        $referenceId = $this->generateIntegerReference();


        $this->createTransactionNew($adminId, $user->id, 'Réception', 'COC', $this->amount, $referenceId, 'Réception d\'argent');
        $this->createTransactionNew($adminId, $user->id, 'Envoie', 'Compte virtuel', $this->amount, $referenceId, 'Envoie d\'argent');

        // session()->flash('success', 'Le compte de l\'agent a été rechargé avec succès.');

        // Notification de succès
        $this->dispatch(event: 'swal:toast');

        $this->reset(['user_id', 'amount', 'search']);

        return redirect()->route('admin.porte-feuille');
    }

    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_admin_id = $senderId;
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
        return view('livewire.recharge-client', [
            'users' => $this->users,
        ]);
    }


}
