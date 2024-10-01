<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;



class ProjetList extends Component
{
    public $projets; // Variable pour stocker les projets
    public $selectedProjet;

    public function mount()
    {
        // Récupérer tous les projets à partir de la base de données
        $this->projets = Projet::with('demandeur')->get();
    }
   

    public function render()
    {
        return view('livewire.projet-list', [
            'projets' => $this->projets
        ]);
    }
}
