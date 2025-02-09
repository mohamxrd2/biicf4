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

    public function render()
    {
        return view('livewire.tontine');
    }
}
