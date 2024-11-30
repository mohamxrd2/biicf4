<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransacWallet extends Component
{
    public $adminID;
    public $limit = 10; // Nombre initial de transactions à afficher

    public function mount()
    {
        $this->adminID = Auth::guard('admin')->id();
    }

    /**
     * Méthode pour charger plus de transactions lorsque l'utilisateur clique sur "Voir plus".
     */
    public function loadMore()
    {
        // Incrémente la limite de transactions à afficher de 5
        $this->limit += 10;
    }

    /**
     * Récupère les transactions à afficher en fonction de la limite.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Récupérer les transactions avec les relations nécessaires
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) {
                $query->where('sender_admin_id', $this->adminID)
                    ->orWhere('receiver_admin_id', $this->adminID);
            })
            ->orderBy('created_at', 'DESC')
            ->limit($this->limit)
            ->get();

        // Calculer le nombre total de transactions disponibles
        $transactionsCount = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) {
                $query->where('sender_admin_id', $this->adminID)
                    ->orWhere('receiver_admin_id', $this->adminID);
            })
            ->count();

        // Vérifier s'il y a encore des transactions à charger
        $hasMoreTransactions = $transactionsCount > $this->limit;

        // Retourner la vue avec les données nécessaires
        return view('livewire.transac-wallet', [
            'transactions' => $transactions,
            'hasMoreTransactions' => $hasMoreTransactions,
            'adminId' => $this->adminID
        ]);
    }
}
