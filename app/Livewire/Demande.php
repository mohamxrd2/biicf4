<?php

namespace App\Livewire;

use App\Models\Psap;
use App\Models\User;
use Livewire\Component;
use App\Models\Livraisons;
use App\Models\RetraitRib;
use App\Models\Deposit; // Assurez-vous que le modèle Deposit est bien importé

class Demande extends Component
{
    public $livraisons;
    public $psaps;
    public $deposits;

    public $activeTab = 'livraisons';

    public $retraits;

    public function mount()
    {
        // Récupération des livraisons et psaps avec les utilisateurs associés
        $this->livraisons = Livraisons::with('user')->latest()->get();
        $this->psaps = Psap::with('user')->latest()->get();
        $this->activeTab = 'livraisons';

        // Récupère les données des dépôts de la table Deposit avec l'utilisateur associé
        $this->deposits = Deposit::with('user') // Assurez-vous que la relation 'user' est définie dans le modèle Deposit
            ->latest() // Trie les résultats par date de création décroissante
            ->get()
            ->map(function ($deposit) {
                // Ajoute les informations de l'utilisateur pour chaque dépôt
                $deposit->user->name = $deposit->user ? $deposit->user->name : 'Utilisateur inconnu';
                return $deposit;
            });
        $this->retraits = RetraitRib::with('user')->latest()->get()->map(function ($retrait) {
            $retrait->user->name = $retrait->id_user ? $retrait->user->name : 'Utilisateur inconnu';
            return $retrait;
        });
    }

    public function render()
    {
        return view('livewire.demande');
    }
}
