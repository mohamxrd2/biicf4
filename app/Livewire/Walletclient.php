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
    public function envoie()
    {
        $this->dispatch('navigate', 'envoie');
    }
    public function retrait()
    {
        $this->dispatch('navigate', 'retrait');
    }
    public function deposit()
    {
        $this->dispatch('navigate', 'deposit');
    }
    public function transfert()
    {
        $this->dispatch('navigate', 'transfert');
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
    public function formatAccountNumber($accountNumber)
    {
        // Séparer les 8 premiers chiffres et masquer les 8 derniers
        $visiblePart = substr($accountNumber, 0, 8);  // Garde les 8 premiers chiffres
        $maskedPart = '**** ****'; // Masque les 8 derniers chiffres
    
        // Ajouter un espace tous les 4 chiffres pour la partie visible
        $formattedVisiblePart = trim(chunk_split($visiblePart, 4, ' '));
    
        // Retourner le numéro de compte formaté
        return $formattedVisiblePart . ' ' . $maskedPart;
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
