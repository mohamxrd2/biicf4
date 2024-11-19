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

    public $credits;
    public $totalCredits;
    public $totalCreditsRembourses;
    public $pourcentageRemboursement;
    public $pourcentageRemboursementParCredit;

    public $projets;
    public $totalProjets;
    public $totalProjetsRembourses;
    public $pourcentageRemboursementProjets;
    public $pourcentageRemboursementParProjet;

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

        // Récupérer les projets associés à cet utilisateur
        $this->projets = projets_accordé::where('emprunteur_id', $userId)->get();

        // Initialiser les totaux pour calculs globaux
        $this->totalProjets = 0;
        $this->totalProjetsRembourses = 0;
        $this->pourcentageRemboursementParProjet = [];

        // Calculs pour chaque projet
        foreach ($this->projets as $projet) {
            // Calculer le montant remboursé pour ce projet
            $montantRembourse = $projet->montant - $projet->montan_restantt;

            // Ajouter les montants au total global
            $this->totalProjets += $projet->montant;
            $this->totalProjetsRembourses += $montantRembourse;

            // Calculer le pourcentage de remboursement
            $pourcentageRemboursement = $projet->montant > 0
                ? ($montantRembourse / $projet->montant) * 100
                : 0; // Éviter la division par zéro

            // Stocker le pourcentage pour ce projet
            $this->pourcentageRemboursementParProjet[$projet->id] = $pourcentageRemboursement;
        }

        // Calcul global du pourcentage de remboursement
        $this->pourcentageRemboursementProjets = $this->totalProjets > 0
            ? ($this->totalProjetsRembourses / $this->totalProjets) * 100
            : 0; // Éviter la division par zéro



        // Récupérer les crédits associés
        $this->credits = credits_groupé::where('emprunteur_id', $userId)->get();

        // Initialiser les totaux pour calcul global, si nécessaire
        $this->totalCredits = 0;
        $this->totalCreditsRembourses = 0;

        // Stocker les pourcentages de remboursement par crédit
        $this->pourcentageRemboursementParCredit = [];

        // Parcourir les crédits pour calculer les montants et les pourcentages individuellement
        foreach ($this->credits as $credit) {
            // Calculer le montant total remboursé pour ce crédit
            $montantRembourse = $credit->montant - $credit->montan_restantt;

            // Ajouter au total global pour calculs agrégés (si nécessaire)
            $this->totalCredits += $credit->montant;
            $this->totalCreditsRembourses += $montantRembourse;

            // Calculer le pourcentage pour ce crédit
            $pourcentageRemboursement = $credit->montant > 0
                ? ($montantRembourse / $credit->montant) * 100
                : 0; // Éviter la division par zéro

            // Stocker le pourcentage pour ce crédit
            $this->pourcentageRemboursementParCredit[$credit->id] = $pourcentageRemboursement;
        }

        // Si besoin, calculer un pourcentage global (par exemple, la moyenne)
        if ($this->totalCredits > 0) {
            $this->pourcentageRemboursement = ($this->totalCreditsRembourses / $this->totalCredits) * 100;
        } else {
            $this->pourcentageRemboursement = 0;
        }


        // RECUPERER les transactions impliquant l'utilisateur authentifié
        $this->transactions = transactions_remboursement::with(['emprunteur', 'investisseur']) // Remplacer par les relations
            ->where(function ($query) use ($userId) {
                $query->where('emprunteur_id', $userId)
                    ->orWhere('investisseur_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $this->transacCount = transactions_remboursement::where(function ($query) use ($userId) {
            $query->where('emprunteur_id', $userId)
                ->orWhere('investisseur_id', $userId);
        })->count();
    }

    public function render()
    {
        return view('livewire.cfa');
    }
}
