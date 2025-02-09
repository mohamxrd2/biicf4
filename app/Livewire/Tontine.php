<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class Tontine extends Component
{
    public $tontineStart;
    public $amount;
    public $frequency;
    public $end_date;


    public function mount()
    {
       $this->tontineStart = true;
    }
    // Propriété calculée pour le gain potentiel
    public function getPotentialGainProperty()
    {
        if (!$this->amount || !$this->frequency || !$this->end_date) {
            return 0; // Retourne 0 si une des valeurs est manquante
        }

        $startDate = Carbon::now();
        $endDate = Carbon::parse($this->end_date);
        $periods = 0;

        // Calcul de la période en fonction de la fréquence
        switch ($this->frequency) {
            case 'quotidienne':
                $periods = $startDate->diffInDays($endDate);
                break;
            case 'hebdomadaire':
                $periods = $startDate->diffInWeeks($endDate);
                break;
            case 'mensuelle':
                $periods = $startDate->diffInMonths($endDate);
                break;
            default:
                $periods = 0;
        }

        // Ajout du mois supplémentaire pour les frais de gestion
        $totalPeriods = $periods + 1;

        // Gain potentiel = montant * nombre de périodes
        return $this->amount * $periods;
    }
    public function render()
    {
        return view('livewire.tontine');
    }
}
