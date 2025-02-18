<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cotisation;
use App\Models\Tontines;

class DetailTontine extends Component
{
    public $id;
    public $tontine;
    public $pourcentage;
    public $cts_reussi;
    public $transCotisation;
    public $cts_sum;
    public $hasMoreTransactions = false;
    public $transactionsLimit = 5;
    public $transactionsOffset = 0;

    public function mount($id)
    {
        $this->id = $id;

        // Récupération de la tontine
        $this->tontine = Tontines::findOrFail($this->id);

        // Récupération des cotisations réussies
        $cotisationsReussies = Cotisation::where('tontine_id', $this->id)
            ->where('statut', 'payé')
            ->get();

        // Comptage et somme des montants des cotisations réussies
        $this->cts_reussi = $cotisationsReussies->count();
        $this->cts_sum = $cotisationsReussies->sum('montant');

        // Gestion du risque de division par zéro
        $nombreCotisations = $this->tontine->nombre_cotisations ?: 1;
        $this->pourcentage = ($this->cts_reussi / $nombreCotisations) * 100;

        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transCotisation = Cotisation::where('tontine_id', $this->id)
            ->latest('created_at')
            ->offset($this->transactionsOffset)
            ->limit($this->transactionsLimit)
            ->with('user')
            ->get();

        // Mise à jour du compteur pour la pagination
        $totalTransactions = Cotisation::where('tontine_id', $this->id)->count();
        $this->hasMoreTransactions = ($this->transactionsOffset + $this->transactionsLimit) < $totalTransactions;
    }

    public function loadMoreTransactions()
    {
        $this->transactionsOffset += $this->transactionsLimit;

        $newTransactions = Cotisation::where('tontine_id', $this->id)
            ->latest('created_at')
            ->offset($this->transactionsOffset)
            ->limit($this->transactionsLimit)
            ->with('user')
            ->get();

        // Append new transactions to existing ones
        $this->transCotisation = $this->transCotisation->concat($newTransactions);

        // Check if there are more transactions to load
        $totalTransactions = Cotisation::where('tontine_id', $this->id)->count();
        $this->hasMoreTransactions = ($this->transactionsOffset + $this->transactionsLimit) < $totalTransactions;
    }

    public function render()
    {
        return view('livewire.detail-tontine');
    }
}
