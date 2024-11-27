<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Admin;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\ComissionAdmin;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet as AdminWallet;

class Wallet extends Component
{
    public $amount;

    public $totalEnv;

    public $totalRecu;

    public $comissions;

  

    public function mount()
    {
        $this->totalEnv = Transaction::where('sender_admin_id', Auth::guard('admin')->id())
            ->where('type', 'Envoie')
            ->sum('amount');
        
        $this->totalRecu = Transaction::where('receiver_admin_id', Auth::guard('admin')->id())
             ->where('type', 'Reception')
             ->sum('amount');
        
        $this->comissions = ComissionAdmin::where('admin_id', 1)->first();
       
       
    }

    protected $rules = [
        'amount' => 'required|numeric|min:0',
    ];

    public function deposit()
    {
        $this->validate();

        $adminId = Auth::guard('admin')->id();

        // Créer une nouvelle transaction
        // $transaction = new Transaction();
        // $transaction->receiver_admin_id = $adminId;
        // $transaction->type = 'Depot';
        // $transaction->amount = $this->amount;
        // $transaction->save();

        // Mettre à jour le solde du portefeuille de l'administrateur
        $adminWallet = AdminWallet::where('admin_id', $adminId)->first();
        $adminWallet->increment('balance', $this->amount);

        // Réinitialiser le champ du formulaire après le dépôt
        $this->amount = null;

        // Rediriger avec un message de succès
        // session()->flash('success', 'Dépôt effectué avec succès.');

        // // Émettre un événement pour mettre à jour les données dans d'autres composants si nécessaire
        // $this->emit('walletUpdated');

        // // Fermer le modal après dépôt
        // $this->emit('closeModal');

        $this->resetForm();
        return redirect()->route('admin.porte-feuille')->with('success', 'Agent ajouté avec succès!');
    }

    private function resetForm()
    {
        $this->amount = '';
    }
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


        return view('livewire.wallet', compact('adminWallet', 'agents', 'users', 'agentCount', 'userCount', 'adminId'));
    }
    
    public function navigateToClient()
    {
        $this->dispatch('navigate', 'rechargeClient');
    }

    public function navigateToContact()
    {
        $this->dispatch('navigate', 'rechargeAgent');
    }
}
