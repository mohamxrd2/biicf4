<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class AgentShow extends Component
{
    use WithPagination;

    public $username;
    public $agent;
    public $wallet;
    public $users;
    public $userCount;
    public $adminId;
    public $transacCount;

    public function mount($username)
    {
        $this->username = $username;
        $this->loadData();
    }

    public function loadData()
    {
        // Récupérer les détails de l'agent en fonction de son username
        $this->agent = Admin::where('username', $this->username)->firstOrFail();

        $this->adminId = $this->agent->id;
        $this->wallet = Wallet::where('admin_id', $this->agent->id)->first();
        $this->users = User::where('admin_id', $this->agent->id)->get();
        $this->userCount = User::where('admin_id', $this->agent->id)->count();
    }

    public function render()
    {
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) {
                $query->where('sender_admin_id', $this->adminId)
                    ->orWhere('receiver_admin_id', $this->adminId);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $this->transacCount = $transactions->count();

        return view('livewire.agent-show', [
            'agent' => $this->agent,
            'wallet' => $this->wallet,
            'users' => $this->users,
            'userCount' => $this->userCount,
            'adminId' => $this->adminId,
            'transactions' => $transactions,
            'transacCount' => $this->transacCount,
        ]);
    }
}
