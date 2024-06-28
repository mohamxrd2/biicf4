<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet as AdminWallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Wallet extends Component
{
    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function render()
    {

        $adminId = Auth::guard('admin')->id();

        if (!$adminId) {
            abort(403, 'Admin not authenticated');
        }

        // Récupérer le portefeuille de l'administrateur connecté
        $adminWallet = AdminWallet::where('admin_id', $adminId)->first();

        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($adminId) {
                $query->where('sender_admin_id', $adminId)
                    ->orWhere('receiver_admin_id', $adminId);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $transacCount = $transactions->count();

        // Récupérer les 5 derniers agents
        $agents = Admin::where('admin_type', 'agent')
            ->orderBy('created_at', 'DESC')
            ->get();

        $agentCount = $agents->count();

        // Récupérer les 5 derniers utilisateurs
        $users = User::with('admin')
            ->orderBy('created_at', 'DESC')
            ->get();

        $userCount = $users->count();


        return view('livewire.wallet', compact('adminWallet', 'transactions', 'transacCount', 'agents', 'users', 'agentCount', 'userCount', 'adminId'));
    }
}
