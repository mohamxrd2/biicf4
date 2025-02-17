<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tontines;
use App\Models\Cotisation;

class DetailTontine extends Component
{
    public $id;
    public $tontine;
    public $poucentage;
    public $cts_reussi;

    public function mount($id)
    {
        $this->id = $id;
        // Fetch the tontine data from the database and pass it to the view.
        $this->tontine = Tontines::find($this->id);

        $this->cts_reussi = Cotisation::where('tontine_id', $this->id)
            ->where('statut', 'reussi')
            ->count();
        $this->poucentage = ($this->cts_reussi / $this->tontine->nombre_cotisations) * 100;
    }
    public function render()
    {
        return view('livewire.detail-tontine');
    }
}
