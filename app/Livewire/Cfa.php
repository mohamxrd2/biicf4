<?php

namespace App\Livewire;

use App\Models\credits;
use App\Models\transactions_remboursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Cfa extends Component
{

    public $credits; // Propriété publique pour stocker les crédits
    public $totalCredits; // Propriété publique pour stocker les crédits
    public $totalCreditsRembourses; // Propriété publique pour stocker les crédits
    public $transacCount;
    public $transactions;
    public $pourcentageRemboursement;

    public function mount()
    {
        $this->refreshData();
    }

    #[On('echo:portions-journalieres,PortionUpdated')]
    public function refreshData()
    {
        $userId = Auth::id();

        // Récupérer les crédits associés à cet utilisateur
        // Si un utilisateur a des crédits associés, les récupérer
        $this->credits = credits::where('emprunteur_id', $userId)->get();

        $this->totalCredits = credits::where('emprunteur_id', $userId)->sum('montant');

        $this->totalCreditsRembourses = credits::where('emprunteur_id', $userId)
            ->sum(DB::raw('montant - montant_restant'));

        // Calcul du pourcentage de remboursement
        if ($this->totalCredits > 0) {
            $this->pourcentageRemboursement = ($this->totalCreditsRembourses / $this->totalCredits) * 100;
        } else {
            $this->pourcentageRemboursement = 0; // éviter la division par zéro
        }
        // Récupérer les transactions impliquant l'utilisateur authentifié
        $this->transactions = transactions_remboursement::with(['emprunteur', 'investisseur']) // Remplacer par les relations
            ->where(function ($query) use ($userId) {
                $query->where('emprunteur_id', $userId)
                    ->orWhere('investisseur_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
        Log::info('Transactions involving authenticated user:', ['transactions' => $this->transactions]);

        $this->transacCount = transactions_remboursement::where(function ($query) use ($userId) {
            $query->where('emprunteur_id', $userId)
                ->orWhere('investisseur_id', $userId);
        })->count();
        Log::info('Transaction Count involving authenticated user:', ['transaction_count' => $this->transacCount]);
    }

    public function render()
    {
        return view('livewire.cfa');
    }
}
