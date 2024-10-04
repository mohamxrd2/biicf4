<?php

namespace App\Livewire;

use Livewire\Component;

class Demandecredit extends Component
{
    public $showSection = false;
    public $referenceCode;

    protected $listeners = ['userIsEligible' => 'handleEligibility'];

    public function handleEligibility($isEligible)
    {
        // Si l'utilisateur est éligible, afficher la section
        if ($isEligible) {
            $this->showSection = true;

            // Générer un code de référence de 5 chiffres
            $this->referenceCode = $this->generateReferenceCode();
        }
    }

    // Fonction pour générer un code de référence de 5 chiffres
    private function generateReferenceCode()
    {
        return rand(10000, 99999); // Générer un nombre aléatoire de 5 chiffres
    }

    public function render()
    {
        return view('livewire.demandecredit');
    }
}
