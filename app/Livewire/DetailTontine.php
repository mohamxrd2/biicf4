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
    public $cts_sum;

    public function mount($id)
    {
        $this->id = $id;

        // Récupération de la tontine
        $this->tontine = Tontines::findOrFail($this->id);

        // Récupération des cotisations réussies
        $cotisationsReussies = Cotisation::where('tontine_id', $this->id)
            ->where('statut', 'reussi')
            ->get();

        // Comptage et somme des montants des cotisations réussies
        $this->cts_reussi = $cotisationsReussies->count();
        $this->cts_sum = $cotisationsReussies->sum('montant');

        // Gestion du risque de division par zéro
        $nombreCotisations = $this->tontine->nombre_cotisations ?: 1;
        $this->pourcentage = ($this->cts_reussi / $nombreCotisations) * 100;
    }

    public function render()
    {
        return view('livewire.detail-tontine');
    }
}
