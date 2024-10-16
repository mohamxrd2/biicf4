<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProjetDetails extends Component
{
    public $projet;

    public $images = [];

    
    public function mount($id)
{
    $this->projet = Projet::with('demandeur')->find($id);

    if ($this->projet) {
        $this->images = array_filter([
            $this->projet->photo1,
            $this->projet->photo2,
            $this->projet->photo3,
            $this->projet->photo4,
            $this->projet->photo5 // Ajoutez autant de photos que vous avez dans la base de données
        ]);
    }
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
        return view('livewire.projet-details', ['images' => $this->images]);
    }
    
}
