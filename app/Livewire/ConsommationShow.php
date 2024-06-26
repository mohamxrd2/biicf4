<?php

namespace App\Livewire;

use App\Models\Consommation;
use Livewire\Component;

class ConsommationShow extends Component
{
    public $id;
    public $consommation;

    public function mount($id)
    {
        $this->id = $id;

        // Charger la consommation basée sur l'ID fourni
        $this->consommation = Consommation::findOrFail($id);
    }

    public function accepter()
    {
        // Mettre à jour le statut de la consommation
        $this->consommation->update(['statuts' => 'Accepté']);

        // Rafraîchir les données
        $this->consommation = Consommation::findOrFail($this->id);
    }
    public function refuser()
    {
        // Mettre à jour le statut de la consommation
        $this->consommation->update(['statuts' => 'Refusé']);

        // Rafraîchir les données
        $this->consommation = Consommation::findOrFail($this->id);
    }

    public function render()
    {
        // Charger la consommation pour l'affichage
        $consommations = Consommation::findOrFail($this->id);

        return view('livewire.consommation-show', [
            'consommations' => $consommations
        ]);
    }
}
