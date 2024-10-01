<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;

class ProjetDetails extends Component
{
    public $projet;

    public function mount($id)
    {
        $this->projet = Projet::with('demandeur')->find($id);
    }

    public function accepterProjet()
    {
        // Mettre à jour le statut du projet
        $this->projet->statut = 'approuvé';
        $this->projet->save();

        
    }

    public function refuserProjet()
    {
        // Mettre à jour le statut du projet
        $this->projet->statut = 'rejeté';
        $this->projet->save();

       
    }

    public function render()
    {
        return view('livewire.projet-details');
    }
}
