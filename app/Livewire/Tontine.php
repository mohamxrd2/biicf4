<?php

namespace App\Livewire;

use App\Models\Tontines;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
<<<<<<< HEAD

class Tontine extends Component
{
    public $amount;
    public $frequency;
    public $end_date;
    public $payment_mode;

    protected function rules()
    {
        return [
            'amount' => 'required|numeric|min:1',
            'frequency' => 'required|in:quotidienne,hebdomadaire,mensuelle',
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $minDate = now()->addMonth(); // Minimum 1 mois après aujourd'hui
                    $chosenDate = Carbon::parse($value);

                    if ($chosenDate->lessThan($minDate)) {
                        $fail("La date de fin doit être d'au moins un mois après aujourd'hui (" . $minDate->format('d/m/Y') . ").");
                    }

                    // Si fréquence hebdomadaire, la date doit être un multiple de semaines après le minimum
                    if ($this->frequency === 'hebdomadaire') {
                        $diffFromMin = $minDate->diffInDays($chosenDate);
                        if ($diffFromMin % 7 !== 0) {
                            $fail("Avec une fréquence hebdomadaire, la date de fin doit être un multiple de semaines après la date minimale (" . $minDate->format('d/m/Y') . ").");
                        }
                    }
                }
            ],
            'payment_mode' => 'required|in:mobile_money,virement_bancaire,cash',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedEndDate()
    {
        $chosenDate = Carbon::parse($this->end_date);
        $minDate = now()->addMonth();

        if ($chosenDate->lessThan($minDate)) {
            $this->end_date = $minDate->format('Y-m-d'); // Corrige pour la date minimale valide
        }

        if ($this->frequency === 'hebdomadaire') {
            $diffFromMin = $minDate->diffInDays($chosenDate);
            if ($diffFromMin % 7 !== 0) {
                $this->end_date = $minDate->addWeeks(ceil($diffFromMin / 7))->format('Y-m-d');
            }
        }
    }

    public function initiateTontine()
    {
        $this->validate();

        // Création de la tontine
        Tontines::create([
            'nom' => 'Tontine ' . now()->format('Y-m-d'), // Ajout d'un nom par défaut
            'montant_cotisation' => $this->amount,
            'montant_total' => $this->amount,
            'frequence' => $this->frequency,
            'date_fin' => $this->end_date,
            'frais_gestion' => $this->payment_mode,
            'user_id' => Auth::id(), // Associe la tontine à l'utilisateur connecté
        ]);

        // Réinitialisation des champs après soumission
        $this->reset();

        // Message de confirmation
        session()->flash('message', 'Tontine créée avec succès !');
    }

=======
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
>>>>>>> c43dd812e4996f38a7138381897c1cd6b40d40f5
    public function render()
    {
        return view('livewire.tontine');
    }
}
