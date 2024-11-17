<?php

namespace App\Livewire;

use App\Models\DemandeCredi;
use App\Models\Projet;
use App\Models\remboursements;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjetFinance extends Component
{
    public $demandecredits;
    public $remboursements;
    public $projets;
    public $totalMontantp;
    public $totalMontant;
    public $interet;
    public $calculInteret;
    public $totalMontants;
    public $countDemandecredits;
    public $countProjets;
    public $countRemboursements;

    public function mount()
    {
        $userId = Auth::id();

        $this->demandecredits = DemandeCredi::where('id_user', $userId)->get();
        $this->remboursements = remboursements::where('id_user', $userId)->get();
        $this->projets = Projet::where('id_user', $userId)->get();

        // Compte le nombre de demandes de crédit et de remboursements
        $this->countDemandecredits = $this->demandecredits->count();
        $this->countRemboursements = $this->remboursements->count();
        $this->countProjets = $this->projets->count();

        // Calcule le total des montants
        $this->totalMontant = $this->demandecredits->sum('montant');
        $this->totalMontants = $this->remboursements->sum('montant_capital');
        $this->totalMontantp = $this->projets->sum('montant');

        // Calcule le total des interet
        $this->interet = $this->remboursements->sum('montant_interet');
        $this->calculInteret = $this->remboursements->sum(function ($remboursement) {
            return ($remboursement->montant_capital * $remboursement->montant_interet) / 100;
        });
            }
    public function render()
    {
        return view('livewire.projet-finance');
    }
}
