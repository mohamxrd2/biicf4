<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TransacWallet extends Component
{
    use WithPagination;
    public function render()
    {
        $adminId = Auth::guard('admin')->id();
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
        ->where(function ($query) use ($adminId) {
            $query->where('sender_admin_id', $adminId)
                ->orWhere('receiver_admin_id', $adminId);
        })
        ->orderBy('created_at', 'DESC')
        ->paginate(5);
        $transacCount = $transactions->count();

        return view('livewire.transac-wallet', compact( 'transactions', 'transacCount', 'adminId'));
    }
}
