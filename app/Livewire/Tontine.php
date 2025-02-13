<?php

namespace App\Livewire;

use App\Models\Tontines;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Tontine extends Component
{
    public $amount;
    public $frequency;
    public $end_date;
    public $payment_mode;
    public $tontineStart;
    public $potentialGain = 0;

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

    public function mount()
    {
        $this->tontineStart = true;
    }
    public function updatedEndDate()
    {
        if (!$this->end_date) {
            return;
        }

        $chosenDate = Carbon::parse($this->end_date);
        $minDate = now()->addMonth();

        if ($chosenDate->lessThan($minDate)) {
            $this->end_date = $minDate->format('Y-m-d'); // Ajuste la date minimale
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
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'frequency' => 'required|in:quotidienne,hebdomadaire,mensuelle',
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $minDate = now()->addMonth();
                    $chosenDate = Carbon::parse($value);

                    if ($chosenDate->lessThan($minDate)) {
                        $fail("La date de fin doit être d'au moins un mois après aujourd'hui (" . $minDate->format('d/m/Y') . ").");
                    }

                    if ($this->frequency === 'hebdomadaire') {
                        $diffFromMin = $minDate->diffInDays($chosenDate);
                        if ($diffFromMin % 7 !== 0) {
                            $fail("Avec une fréquence hebdomadaire, la date de fin doit être un multiple de semaines après la date minimale (" . $minDate->format('d/m/Y') . ").");
                        }
                    }
                }
            ],
        ]);

        $this->updatedEndDate();

        // Vérifier si l'utilisateur a déjà une tontine en cours
        $existingTontine = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', now()) // Tontines qui ne sont pas encore terminées
            ->exists();

        if ($existingTontine) {
            session()->flash('error', 'Vous avez déjà une tontine en cours. Vous ne pouvez pas en créer une nouvelle tant que l’ancienne n’est pas terminée.');
            return;
        }

        // Calcul du nombre de dépôts attendus
        $startDate = now();
        $endDate = Carbon::parse($this->end_date);
        $nbre_depot = match ($this->frequency) {
            'quotidienne' => $startDate->diffInDays($endDate),
            'hebdomadaire' => $startDate->diffInWeeks($endDate),
            'mensuelle' => $startDate->diffInMonths($endDate),
            default => 0
        };

        $frais_gestion = ($nbre_depot * $this->amount) / 30; // Calcul des frais de gestion

        Tontines::create([
            'nom' => 'Tontine ' . now()->format('Y-m-d'),
            'montant_cotisation' => $this->amount,
            'montant_total' => $this->amount * $nbre_depot,
            'frequence' => $this->frequency,
            'date_fin' => $this->end_date,
            'next_payment_date' => now(),
            'frais_gestion' => $frais_gestion,
            'user_id' => Auth::id(),
        ]);

        $this->reset();

        session()->flash('message', 'Tontine créée avec succès !');
    }




    public function render()
    {
        return view('livewire.tontine');
    }
}
