<?php

namespace App\Livewire;

use Livewire\Component;

class Demandecredit extends Component
{
    public $showSection = false;
    public $referenceCode;

    public $price;
    public $duration;
    public $financementType;
    public $username;
    public $bailleur;
    public $startDate;
    public $startTime;
    public $endDate;
    public $endTime;
    public $roi;

    protected $rules = [
        'price' => 'required|numeric',
        'duration' => 'required|numeric',
        'financementType' => 'required|string',
        'username' => 'nullable|string',
        'bailleur' => 'nullable|string',
        'startDate' => 'required|date',
        'startTime' => 'required|date_format:H:i',
        'endDate' => 'required|date',
        'endTime' => 'required|date_format:H:i',
        'roi' => 'required|numeric',
    ];

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
    public function submit()
    {
        dd($this->validate());

        // Logique pour enregistrer les données ou faire d'autres actions
        // ...

        session()->flash('message', 'Demande soumise avec succès !');
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
