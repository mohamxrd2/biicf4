<?php

namespace App\Livewire;

use App\Models\Cedd;
use App\Models\Cefp;
use App\Models\Cfa;
use App\Models\Coi;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Livewire\Component;

class TransfertAccount extends Component
{
    public $amount = '';      // Montant à transférer
    public $account1 = '';    // Compte de départ
    public $account2 = '';    // Compte de réception
    public $userWallet;
    public $coi, $cfa, $cedd, $cefd;

    // Règles de validation
    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'account1' => 'required|string|different:account2', // Assurez-vous que les comptes ne sont pas identiques
        'account2' => 'required|string',
    ];

    public function mount()
    {
        $userId = Auth::guard('web')->id();
        Log::info('User ID:', ['user_id' => $userId]);
        $this->userWallet = Wallet::where('user_id', $userId)->first();
        Log::info('User Wallet:', ['wallet' => $this->userWallet]);

        $this->coi = Coi::where('id_wallet', $this->userWallet->id)->first();
        $this->cfa = Cfa::where('id_wallet', $this->userWallet->id)->first();
        $this->cedd = Cedd::where('id_wallet', $this->userWallet->id)->first();
        $this->cefd = Cefp::where('id_wallet', $this->userWallet->id)->first();
    }

  
    public function submitTransfert()
{
    $this->validate(); // Valide les données

    // Récupérer les comptes de départ et de réception
    $accountFrom = $this->getAccount($this->account1);
    $accountTo = $this->getAccount($this->account2);

    if (!$accountFrom || !$accountTo) {
        session()->flash('errorMessage', 'Comptes non valides.');
        return;
    }

    // Vérifier le solde en fonction du type de compte
    $fromBalance = ($this->account1 === 'COC') ? $accountFrom->balance : $accountFrom->Solde;
    $toBalance = ($this->account2 === 'COC') ? $accountTo->balance : $accountTo->Solde;

    // Vérifier que le compte de départ a suffisamment de fonds
    if ($fromBalance < $this->amount) {
        session()->flash('errorMessage', 'Fonds insuffisants dans le compte de prélèvement.');
        return;
    }

    try {
        // Démarrer une transaction
       

        // Débiter le compte de départ
        if ($this->account1 === 'COC') {
            $accountFrom->balance -= $this->amount;
        } else {
            $accountFrom->Solde -= $this->amount;
        }
        $accountFrom->save();

        // Créditer le compte de réception
        if ($this->account2 === 'COC') {
            $accountTo->balance += $this->amount;
        } else {
            $accountTo->Solde += $this->amount;
        }
        $accountTo->save();

        
        
        
       

        // Message de succès
        session()->flash('successMessage', 'Transfert effectué avec succès.');

        // Réinitialiser les champs
        $this->reset(['amount', 'account1', 'account2']);

        // Recharger la page
        return redirect()->to(request()->header('Referer'));
    } catch (\Exception $e) {
        // Annuler la transaction en cas d'erreur
     
        Log::error('Erreur lors du transfert :', ['error' => $e->getMessage()]);
        session()->flash('errorMessage', 'Une erreur s\'est produite lors du transfert. Veuillez réessayer.');
    }
}
    private function getAccount($accountType)
    {
        // Retourne le compte en fonction du type sélectionné
        switch ($accountType) {
            case 'COC':
                return $this->userWallet;
            case 'COI':
                return $this->coi;
            case 'CFA':
                return $this->cfa;
            case 'CEDD':
                return $this->cedd;
            case 'CEFP':
                return $this->cefd;
            default:
                return null;
        }
    }

    public function render()
    {
        return view('livewire.transfert-account');
    }
}
