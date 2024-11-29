<?php
namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransacWallet extends Component
{
    public $adminID;
    public $limit = 5; // Nombre initial de transactions à afficher

    public function mount()
    {
        $this->adminID = Auth::guard('admin')->id();
    }

    /**
     * Méthode pour charger plus de transactions lorsque l'utilisateur clique sur "Voir plus".
     */
    public function loadMore()
    {
  
        // Incrémenter la limite de transactions à afficher de 5
        $this->limit += 5;

        // Simuler le délai de chargement avant de cacher le spinner
 
    }

    /**
     * Vérifie s'il y a encore des transactions à charger.
     *
     * @return bool
     */
    public function hasMoreTransactions()
    {
        // Vérifie si le nombre de transactions retournées dépasse la limite actuelle
        $transactionsCount = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) {
                $query->where('sender_admin_id', $this->adminID)
                    ->orWhere('receiver_admin_id', $this->adminID);
            })
            ->count();

        return $transactionsCount > $this->limit;
    }

    /**
     * Récupère les transactions à afficher en fonction de la limite.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) {
                $query->where('sender_admin_id', $this->adminID)
                    ->orWhere('receiver_admin_id', $this->adminID);
            })
            ->orderBy('created_at', 'DESC')
            ->limit($this->limit)
            ->get();

        $transacCount = $transactions->count();

        return view('livewire.transac-wallet', [
            'transactions' => $transactions,
            'transacCount' => $transacCount,
            'adminId' => $this->adminID
        ]);
    }
}
