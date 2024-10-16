<?php

namespace App\Livewire;

use App\Models\Cedd;
use App\Models\Cefp;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Walletclient extends Component
{
    public $userWallet;
    public $user;
    public $coi;
    public $cfa;
    public $cedd;
    public $cefd;

    public $currentPage = 'transaction';

    protected $listeners = ['navigate' => 'setPage'];

    public function setPage($page)
    {
        $this->currentPage = $page;
    }

    #[On('refreshComponent')]
    public function transfert()
    {
        $this->dispatch('navigate', 'transfert');
    }
    public function retrait()
    {
        $this->dispatch('navigate', 'retrait');
    }
    public function mount()
    {
        $userId = Auth::guard('web')->id();

        // Récupérer l'utilisateur
        $this->user = User::find($userId);



        $this->wallet();
    }
    #[On('refreshComponent')]
    public function wallet()
    {
        $userId = Auth::guard('web')->id();
        Log::info('User ID:', ['user_id' => $userId]);
        $this->userWallet = Wallet::where('user_id', $userId)->first();
        Log::info('User Wallet:', ['wallet' => $this->userWallet]);

        // Récupérer l'enregistrement dans la table Coi en fonction de id_user
        $this->coi = Coi::where('id_wallet', $this->userWallet->id)->first();
        $this->cfa = Cfa::where('id_wallet', $this->userWallet->id)->first();
        $this->cedd = Cedd::where('id_wallet', $this->userWallet->id)->first();
        $this->cefd = Cefp::where('id_wallet', $this->userWallet->id)->first();

    }

    #[On('refreshComponent')]
    public function render()
    {
        $userId = Auth::guard('web')->id();
        Log::info('User ID:', ['user_id' => $userId]);



        // Récupérer les utilisateurs à exclure l'utilisateur authentifié
        $users = User::with('admin')
            ->where('id', '!=', $userId) // Exclure l'utilisateur authentifié
            ->orderBy('created_at', 'DESC')
            ->get();
        Log::info('Users (excluding authenticated user):', ['users' => $users]);

        $userCount = User::where('id', '!=', $userId)->count();
        Log::info('User Count (excluding authenticated user):', ['user_count' => $userCount]);

        // Récupérer les transactions impliquant l'utilisateur authentifié
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_user_id', $userId)
                    ->orWhere('receiver_user_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
        Log::info('Transactions involving authenticated user:', ['transactions' => $transactions]);

        $transacCount = Transaction::where(function ($query) use ($userId) {
            $query->where('sender_user_id', $userId)
                ->orWhere('receiver_user_id', $userId);
        })->count();
        Log::info('Transaction Count involving authenticated user:', ['transaction_count' => $transacCount]);

        return view('livewire.walletclient', compact('users', 'userCount', 'transactions', 'transacCount', 'userId'));
    }
}
