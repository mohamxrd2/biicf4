<?php

namespace App\Livewire;

use App\Models\DemandeCredi;
use App\Models\remboursements;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjetFinance extends Component
{
    public $demandecredits;
    public $remboursements;
    public $totalMontant;
    public $totalMontants;
    public $countDemandecredits;
    public $countRemboursements;

    public function mount()
    {
        $userId = Auth::id();

        $this->demandecredits = DemandeCredi::where('id_user', $userId)->get();
        $this->remboursements = remboursements::where('id_user', $userId)->get();

        // Compte le nombre de demandes de crédit et de remboursements
        $this->countDemandecredits = $this->demandecredits->count();
        $this->countRemboursements = $this->remboursements->count();
        
        // Calcule le total des montants
        $this->totalMontant = $this->demandecredits->sum('montant');
        $this->totalMontants = $this->remboursements->sum('montant_capital');
    }
    public function render()
    {
        return view('livewire.projet-finance');
    }
}
