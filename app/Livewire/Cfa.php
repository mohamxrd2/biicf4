<?php

namespace App\Livewire;

use App\Models\credits;
use App\Models\credits_groupé;
use App\Models\projets_accordé;
use App\Models\transactions_remboursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Cfa extends Component
{

    public $creditsGroupe; // Propriété publique pour stocker les crédits
    public $projets; // Propriété publique pour stocker les crédits
    public $totalCreditsGroupe; // Propriété publique pour stocker les crédits
    public $totalCreditsRembourses; // Propriété publique pour stocker les crédits
    public $totalprojets; // Propriété publique pour stocker les crédits
    public $totalprojetsRembourses; // Propriété publique pour stocker les crédits
    public $totalCreditsRemboursesGroupe;
    public $pourcentageRemboursementGroupe;
    public $pourcentageRemboursementprojets;
    public $transacCount;
    public $transactions;

    public function mount()
    {
        $this->refreshData();
    }

    #[On('echo:portions-journalieres,PortionUpdated')]
    public function refreshData()
    {
        $userId = Auth::id();

        // Récupérer les projets associés à cet utilisateur; Si un utilisateur a des projets associés, les récupérer
        $this->projets = projets_accordé::where('emprunteur_id', $userId)->get();

        $this->totalprojets = $this->projets->sum('montant');
        $this->totalprojetsRembourses = $this->projets->sum(function ($projet) {
            return $projet->montant - $projet->montant_restant;
        });

        // Calcul du pourcentage de remboursement
        if ($this->totalprojets > 0) {
            $this->pourcentageRemboursementprojets = ($this->totalprojetsRembourses / $this->totalprojets) * 100;
        } else {
            $this->pourcentageRemboursementprojets = 0; // éviter la division par zéro
        }

        // Récupérer les CREDITS ASSOCIER GROUPER
        $this->creditsGroupe = credits_groupé::where('emprunteur_id', $userId)->get();
        // Calculer le montant total des crédits et le montant total remboursé
        $this->totalCreditsGroupe = $this->creditsGroupe->sum('montant');
        $this->totalCreditsRemboursesGroupe = $this->creditsGroupe->sum(function ($creditsGroupe) {
            return $creditsGroupe->montant - $creditsGroupe->montan_restantt;
        });
        // Calcul du pourcentage de remboursement global
        if ($this->totalCreditsGroupe > 0) {
            $this->pourcentageRemboursementGroupe = ($this->totalCreditsRemboursesGroupe / $this->totalCreditsGroupe) * 100;
        } else {
            $this->pourcentageRemboursementGroupe = 0; // éviter la division par zéro
        }

        // RECUPERER les transactions impliquant l'utilisateur authentifié
        $this->transactions = transactions_remboursement::with(['emprunteur', 'investisseur']) // Remplacer par les relations
            ->where(function ($query) use ($userId) {
                $query->where('emprunteur_id', $userId)
                    ->orWhere('investisseur_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
        // Log::info('Transactions involving authenticated user:', ['transactions' => $this->transactions]);

        $this->transacCount = transactions_remboursement::where(function ($query) use ($userId) {
            $query->where('emprunteur_id', $userId)
                ->orWhere('investisseur_id', $userId);
        })->count();

        // Log::info('Transaction Count involving authenticated user:', ['transaction_count' => $this->transacCount]);
    }

    public function render()
    {
        return view('livewire.cfa');
    }
}
