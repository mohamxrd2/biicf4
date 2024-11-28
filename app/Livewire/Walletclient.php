<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\Crp;
use App\Models\Cedd;
use App\Models\Cefp;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\On;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Walletclient extends Component
{
    public $userWallet;
    public $user;
    public $coi;
    public $cfa;
    public $cedd;
    public $cefd;
    public $crp;

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

        // Récupérer l'enregistrement dans la table Coi en fonction de id_user
        $this->coi = Coi::where('id_wallet', $this->userWallet->id)->first();
        $this->cfa = Cfa::where('id_wallet', $this->userWallet->id)->first();
        $this->cedd = Cedd::where('id_wallet', $this->userWallet->id)->first();
        $this->cefd = Cefp::where('id_wallet', $this->userWallet->id)->first();
        $this->crp = Crp::where('id_wallet', $this->userWallet->id)->first();

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

      /**
     * Calcule la somme des transactions de type 'Reception' pour l'utilisateur connecté sur les 30 derniers jours
     *
     * @return float
     */
    public function getReceptionTransactionSumForLast30Days()
    {
        $userId = Auth::guard('web')->id();

        // Récupérer la somme des montants des transactions de type 'Reception' pour l'utilisateur connecté
        $sum = Transaction::where('type', 'Réception')
            ->where('receiver_user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');

        return $sum;
    }

      /**
     * Calcule la somme des transactions de type 'Envoie' pour l'utilisateur connecté sur les 30 derniers jours
     *
     * @return float
     */
    public function getEnvoieTransactionSumForLast30Days()
    {
        $userId = Auth::guard('web')->id();

        $sum = Transaction::where('type', 'Envoie')
            ->where('sender_user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');

        return $sum;
    }




    #[On('refreshComponent')]
    public function render()
    {
        $userId = Auth::guard('web')->id();
        Log::info('User ID:', ['user_id' => $userId]);

        $users = User::with('admin')
            ->where('id', '!=', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();

        $userCount = User::where('id', '!=', $userId)->count();
        Log::info('User Count (excluding authenticated user):', ['user_count' => $userCount]);

        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_user_id', $userId)
                    ->orWhere('receiver_user_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $transacCount = Transaction::where(function ($query) use ($userId) {
            $query->where('sender_user_id', $userId)
                ->orWhere('receiver_user_id', $userId);
        })->count();

        $receptionTransactionSum = $this->getReceptionTransactionSumForLast30Days();
        $envoieTransactionSum = $this->getEnvoieTransactionSumForLast30Days();

        return view('livewire.walletclient', compact('users', 'userCount', 'transactions', 'transacCount', 'userId', 'receptionTransactionSum', 'envoieTransactionSum'));
    }
}
