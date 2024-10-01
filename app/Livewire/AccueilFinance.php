<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;
use Illuminate\Support\Carbon;

class AccueilFinance extends Component
{

    public $projets; // Pour stocker les projets

    public $projetRecent;

    public $nombreProjets = 6;

    public $loading = false;



    public function mount()
    {
        // Charger tous les projets avec leurs demandeurs
        $this->projets = Projet::with('demandeur')->take($this->nombreProjets)->get();

        $this->projetRecent = Projet::with('demandeur')->orderBy('created_at', 'desc')->first();
    }

    public function joursRestants()
    {
        // Vérifiez que le projet récent existe
        if ($this->projetRecent) {
            // Récupérez la date de fin depuis 'durer'
            $dateFin = \Carbon\Carbon::parse($this->projetRecent->durer);

            // Récupérer la date actuelle
            $dateActuelle = now();

            // Calculez le nombre de jours restants
            $joursRestants = $dateActuelle->diffInDays($dateFin);

            return $joursRestants >= 0 ? $joursRestants : 0; // Retournez 0 si le projet est déjà terminé
        }

        return 0; // Si aucun projet n'est trouvé, retournez 0
    }

    public function chargerPlus()
    {
        // Définir l'état de chargement à vrai
        $this->loading = true;

        // Augmentez le nombre de projets à afficher
        $this->nombreProjets += 6;

        // Chargez les nouveaux projets
        $this->projets = Projet::with('demandeur')->take($this->nombreProjets)->get();

        // Définir l'état de chargement à faux
        $this->loading = false;
    }




    public function render()
    {
        return view('livewire.accueil-finance', [
            'joursRestants' => $this->joursRestants(), // Passez le nombre de jours restants à la vue
        ]);
    }
}
