<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RechargeAgent extends Component
{
    public $search = '';
    public $agent_id;
    public $amount;
    public $agents = [];

    protected $rules = [
        'agent_id' => 'required',
        'amount' => 'required|numeric',
    ];

    protected $messages = [
        'agent_id.required' => 'Veuillez sélectionner un agent.',
        'amount.required' => 'Veuillez entrer le montant.',
        'amount.numeric' => 'Le montant doit être numérique.',
    ];

    public function mount()
    {
        $this->agents = Admin::all();
    }

    public function updatedSearch()
    {
        $this->agents = Admin::where('username', 'like', '%' . $this->search . '%')->get();
    }

    public function selectAgent($agentId, $agentName)
    {
        $this->agent_id = $agentId;
        $this->search = $agentName;
        $this->agents = [];
    }

    public function recharge()
    {
        $this->validate();

        $agent = Admin::find($this->agent_id);

        if (!$agent) {
            session()->flash('error', 'L\'agent spécifié n\'existe pas.');
            return;
        }

        $adminId = Auth::guard('admin')->id();
        $agentWallet = Wallet::where('admin_id', $agent->id)->first();
        $adminWallet = Wallet::where('admin_id', $adminId)->first();

        if (!$agentWallet || !$adminWallet) {
            session()->flash('error', 'Erreur lors de la récupération des portefeuilles.');
            return;
        }

        if ($adminWallet->balance < $this->amount) {
            session()->flash('error', 'Solde insuffisant pour effectuer la recharge.');
            return;
        }

        $agentWallet->increment('balance', $this->amount);
        $adminWallet->decrement('balance', $this->amount);

        $transaction1 = new Transaction();
        $transaction1->sender_admin_id = $adminId;
        $transaction1->receiver_admin_id = $agent->id;
        $transaction1->type = 'Reception';
        $transaction1->amount = $this->amount;
        $transaction1->save();

        $transaction2 = new Transaction();
        $transaction2->sender_admin_id = $adminId;
        $transaction2->receiver_admin_id = $agent->id;
        $transaction2->type = 'Envoie';
        $transaction2->amount = $this->amount;
        $transaction2->save();

        // session()->flash('success', 'Le compte de l\'agent a été rechargé avec succès.');

        // Notification de succès
        $this->dispatch('swal:toast');

        $this->reset(['agent_id', 'amount', 'search']);

        return redirect()->route('admin.porte-feuille');

    }
    
    public function render()
    {
        return view('livewire.recharge-agent', [
            'agents' => $this->agents,
        ]);
    }
}
