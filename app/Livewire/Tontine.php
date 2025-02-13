<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class Tontine extends Component
{
    public $tontineStart;
    public $amount ;
    public $frequency = '';
    public $end_date = '';
    public $potentialGain = 0;


    public function mount()
    {
       $this->tontineStart = true;
    }
      // Constantes pour les fréquences
      const FREQUENCY_DAYS = [
        'quotidienne' => 1,
        'hebdomadaire' => 7,
        'mensuelle' => 30
    ];
      // Règles de validation
      protected $rules = [
        'amount' => 'required|numeric|min:1000',
        'frequency' => 'required|in:quotidienne,hebdomadaire,mensuelle',
        'end_date' => 'required|date|after:today',
    ];
    // Observateurs pour recalculer le gain potentiel
    public function updatedAmount()
    {
        $this->calculatePotentialGain();
    }

    public function updatedFrequency()
    {
        $this->calculatePotentialGain();
    }

    public function updatedEndDate()
    {
        $this->calculatePotentialGain();
    }
      // Méthode pour calculer le gain potentiel
      private function calculatePotentialGain()
      {
          if (empty($this->amount) || empty($this->frequency) || empty($this->end_date)) {
              $this->potentialGain = 0;
              return;
          }
  
          // Calculer le nombre de jours entre aujourd'hui et la date de fin
          $startDate = Carbon::today();
          $endDate = Carbon::parse($this->end_date);
          $totalDays = $endDate->diffInDays($startDate);
  
          // Obtenir le nombre de jours pour la fréquence sélectionnée
          $frequencyDays = self::FREQUENCY_DAYS[$this->frequency] ?? 1;
  
          // Calculer le nombre de cotisations
          $numberOfContributions = floor($totalDays / $frequencyDays);
  
          // Calculer le gain potentiel
          $this->potentialGain = $numberOfContributions * $this->amount;
      }
      public function initiateTontine()
    {
        $this->validate();
        // Logique pour démarrer la tontine...
    }

   
    public function render()
    {
        return view('livewire.tontine');
    }
}
