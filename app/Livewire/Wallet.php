<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Admin;
use App\Models\Coi;
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

    public $totalBalanceAgent;

    public $totalWalletUser;
    public $totalCOIUser;
    public $totalCEDDUser;
    public $totalCFAUser;

    public $totalCEFDUser;
    public $totalCRPUser;

    public $totalBalanceUsers;



    public function mount()
    {
        $this->totalBalanceAgent = \App\Models\Wallet::whereIn('admin_id', function ($query) {
            $query->select('id')
                ->from('admins')
                ->where('admin_type', 'agent');
        })->sum('balance');

        $this->totalWalletUser = \App\Models\Wallet::whereNotNull('user_id')
            ->sum('balance');
        $this->totalCOIUser = Coi::whereNotNull('id_wallet')->sum('Solde');
        $this->totalCEDDUser = \App\Models\Cedd::whereNotNull('id_wallet')->sum('Solde');
        $this->totalCFAUser = \App\Models\Cfa::whereNotNull('id_wallet')->sum('Solde');
        $this->totalCEFDUser = \App\Models\Cefp::whereNotNull('id_wallet')->sum('Solde');
        $this->totalCRPUser = \App\Models\Crp::whereNotNull('id_wallet')->sum('Solde');



        // Somme des balances des user_id
        $this->totalBalanceUsers = $this->totalWalletUser + $this->totalCOIUser + $this->totalCEDDUser + $this->totalCFAUser + $this->totalCEFDUser + $this->totalCRPUser;


        





        $this->totalRecu = Transaction::where('receiver_admin_id', Auth::guard('admin')->id())
            ->where('type', 'Reception')
            ->sum('amount');

        $this->comissions = ComissionAdmin::where('admin_id', 1)->first();
    }

    protected $rules = [];

    public function deposit()
    {
        $this->validate([
            'amount' => 'required|numeric|min:10000|max:99999999',
        ], [
            'amount.required' => 'Veuillez entrer un montant.',
            'amount.numeric' => 'Le montant doit être numérique.',
            'amount.min' => 'Le montant doit être supérieur à 10 000.',
            'amount.max' => 'Le montant doit être inferieur à 100 Millions'
        ]);

        $adminId = Auth::guard('admin')->id();



        // Mettre à jour le solde du portefeuille de l'administrateur
        $adminWallet = AdminWallet::where('admin_id', $adminId)->first();
        $adminWallet->increment('balance', $this->amount);

        $referenceId = $this->generateIntegerReference();


        $this->createTransactionNew($adminId, $adminId, 'Depot', 'Compte virtuel', $this->amount, $referenceId, 'Rechargement avec agent');

        // Réinitialiser le champ du formulaire après le dépôt
        $this->amount = null;

        // Rediriger avec un message de succès
        session()->flash('success', 'Dépôt effectué avec succès.');

        // // Émettre un événement pour mettre à jour les données dans d'autres composants si nécessaire
        // $this->emit('walletUpdated');

        // // Fermer le modal après dépôt
        // $this->emit('closeModal');

        $this->resetForm();
        return redirect()->route('admin.porte-feuille')->with('success', 'Agent ajouté avec succès!');
    }
    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_admin_id = $senderId;
        $transaction->receiver_admin_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
        $transaction->save();
    }
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
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
