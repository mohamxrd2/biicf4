<?php

namespace App\Livewire;

use App\Models\Psap;
use App\Models\User;
use App\Models\Deposit; // Assurez-vous que le modèle Deposit est bien importé
use Livewire\Component;
use App\Models\Livraisons;

class Demande extends Component
{
    public $livraisons;
    public $psaps;
    public $deposits;

    public function mount()
    {
        // Récupération des livraisons et psaps avec les utilisateurs associés
        $this->livraisons = Livraisons::with('user')->get();
        $this->psaps = Psap::with('user')->get();

        // Récupère les données des dépôts de la table Deposit avec l'utilisateur associé
        $this->deposits = Deposit::with('user') // Assurez-vous que la relation 'user' est définie dans le modèle Deposit
            ->latest() // Trie les résultats par date de création décroissante
            ->get()
            ->map(function ($deposit) {
                // Ajoute les informations de l'utilisateur pour chaque dépôt
                $deposit->user->name = $deposit->user ? $deposit->user->name : 'Utilisateur inconnu';
                return $deposit;
            });
    }

    public function render()
    {
        return view('livewire.demande');
    }
}
