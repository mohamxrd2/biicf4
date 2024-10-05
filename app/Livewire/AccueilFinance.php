<?php

namespace App\Livewire;

use App\Models\AjoutMontant; // Assurez-vous d'importer le modèle AjoutMontant
use App\Models\Projet;
use Livewire\Component;

class AccueilFinance extends Component
{
    public $projets; // Pour stocker les projets
    public $projetRecent;
    public $nombreProjets = 6;
    public $loading = false;

    public function mount()
    {
        // Charger tous les projets avec leurs demandeurs et calculer les valeurs
        $this->projets = Projet::with('demandeur')->take($this->nombreProjets)->get()->map(function ($projet) {
            // Calculer les valeurs pour chaque projet
            $sommeInvestie = AjoutMontant::where('id_projet', $projet->id)->sum('montant');
            $sommeRestante = $projet->montant - $sommeInvestie;
            $pourcentageInvesti = ($projet->montant > 0) ? ($sommeInvestie / $projet->montant) * 100 : 0;
            $nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $projet->id)
                ->distinct()
                ->count('id_invest');

            // Ajouter les valeurs calculées au projet
            $projet->sommeInvestie = $sommeInvestie;
            $projet->sommeRestante = $sommeRestante;
            $projet->pourcentageInvesti = $pourcentageInvesti;
            $projet->nombreInvestisseursDistinct = $nombreInvestisseursDistinct;

            return $projet;
        });

        // Récupérer le projet le plus récent
        $this->projetRecent = Projet::with('demandeur')->orderBy('created_at', 'desc')->first();
        
        // Calculer les valeurs pour le projet le plus récent
        if ($this->projetRecent) {
            $this->calculerValeursProjet($this->projetRecent);
        }
    }

    // Méthode pour calculer les valeurs d'un projet
    private function calculerValeursProjet($projet)
    {
        $sommeInvestie = AjoutMontant::where('id_projet', $projet->id)->sum('montant');
        $sommeRestante = $projet->montant - $sommeInvestie;
        $pourcentageInvesti = ($projet->montant > 0) ? ($sommeInvestie / $projet->montant) * 100 : 0;
        $nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $projet->id)
            ->distinct()
            ->count('id_invest');

        // Ajouter les valeurs calculées au projet
        $projet->sommeInvestie = $sommeInvestie;
        $projet->sommeRestante = $sommeRestante;
        $projet->pourcentageInvesti = $pourcentageInvesti;
        $projet->nombreInvestisseursDistinct = $nombreInvestisseursDistinct;
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
        $this->projets = Projet::with('demandeur')->take($this->nombreProjets)->get()->map(function ($projet) {
            // Calculer les valeurs pour chaque projet
            $this->calculerValeursProjet($projet);
            return $projet;
        });
    
        // Récupérer le projet le plus récent
        $this->projetRecent = Projet::with('demandeur')->orderBy('created_at', 'desc')->first();
        
        // Calculer les valeurs pour le projet le plus récent
        if ($this->projetRecent) {
            $this->calculerValeursProjet($this->projetRecent);
        }
    
        // Définir l'état de chargement à faux
        $this->loading = false;
    }
    

    public function render()
    {
        return view('livewire.accueil-finance', [
            'joursRestants' => $this->joursRestants(), // Passez le nombre de jours restants à la vue
            'projets' => $this->projets, // Passez les projets avec les valeurs calculées à la vue
            'projetRecent' => $this->projetRecent, // Passez le projet le plus récent à la vue
        ]);
    }
}
