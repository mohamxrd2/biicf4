<?php

namespace App\Livewire;

use App\Models\Cotisation;
use App\Models\Tontines;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Tontine extends Component
{
    public $amount;
    public $frequency;
    public $duration;
    public $tontineStart = false;
    public $tontineData;
    public $tontineEnCours;
    public $cotisations;
    public $cotisationsCount;
    public $cotisationSum;
    public $pourcentage;
    public $serverTime;
    public $time;
    public $error;
    public $timestamp;
    public $potentialGain = 0;
    protected $recuperationTimer;
    public $tontineDatas;

    // Tableaux pour stocker les erreurs par champ
    public $errors = [
        'amount' => '',
        'frequency' => '',
        'duration' => ''
    ];

    protected $rules = [
        'amount' => 'required|numeric|min:1000',
        'frequency' => 'required|in:quotidienne,hebdomadaire,mensuelle',
        'duration' => 'required|integer'
    ];

    public function mount()
    {
        $this->recuperationTimer = new RecuperationTimer();
        $this->resetErrors();
        $this->timeServer();

        // Assurer que serverTime est un objet Carbon
        $this->serverTime = $this->timestamp;

        // Vérifier si l'utilisateur a une tontine en cours
        $this->tontineStart = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', $this->serverTime)
            ->exists();

        $this->tontineEnCours = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', $this->serverTime)
            ->first();

        $this->tontineDatas = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', $this->serverTime)
            ->orderBy('created_at', 'desc') // Trie par date de création, du plus récent au plus ancien
            ->get();

        $this->cotisations = Cotisation::where('user_id', Auth::id())
            ->where('tontine_id', $this->tontineEnCours->id)
            ->get();

        $this->cotisationsCount = $this->cotisations->where('statut', 'reussi')->count();
        $this->cotisationSum = $this->cotisations->sum('montant');

        $nombreCotisations = $this->tontineEnCours->nombre_cotisations ?: 1; // Évite la division par zéro

        $this->pourcentage = ($this->cotisationsCount / $nombreCotisations) * 100;
    }

    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();
        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }

    public function resetErrors()
    {
        foreach ($this->errors as $key => $value) {
            $this->errors[$key] = '';
        }
    }

    public function updated($propertyName)
    {
        $this->resetErrors();
        $this->calculatePotentialGain();
    }

    protected function calculatePotentialGain()
    {
        if ($this->amount && $this->duration) {
            $nbre_depot = $this->duration;
            $montant_total = $this->amount * $nbre_depot;
            $frais_gestion = $montant_total / 30;

            // Mise à jour des éléments UI
            $this->dispatch('updateCalculations', [
                'potentialGain' => number_format($montant_total, 0, '.', ' ') . ' FCFA',
                'fraisDeService' => number_format($frais_gestion, 0, '.', ' ') . ' FCFA',
                'endDate' => $this->calculateEndDate()
            ]);
        }
    }

    protected function calculateEndDate()
    {
        if (!$this->frequency || !$this->duration) return '-';

        $startDate = clone $this->serverTime; // Cloner pour éviter de modifier la variable d'origine
        $endDate = match ($this->frequency) {
            'quotidienne' => $startDate->addDays($this->duration),
            'hebdomadaire' => $startDate->addWeeks($this->duration),
            'mensuelle' => $startDate->addMonths($this->duration),
            default => $startDate
        };

        return $endDate->format('d/m/Y');
    }

    public function initiateTontine()
    {
        $this->resetErrors();

        try {
            // Validation de la durée minimale
            $minDuration = match ($this->frequency) {
                'quotidienne' => 30,
                'hebdomadaire' => 4,
                'mensuelle' => 1,
                default => 1,
            };

            // Validations personnalisées
            if (empty($this->amount)) {
                $this->errors['amount'] = 'Le montant est obligatoire.';
            } elseif (!is_numeric($this->amount)) {
                $this->errors['amount'] = 'Le montant doit être un nombre.';
            } elseif ($this->amount < 1000) {
                $this->errors['amount'] = 'Le montant minimum est de 1000 FCFA.';
            }

            if (empty($this->frequency)) {
                $this->errors['frequency'] = 'Veuillez sélectionner une fréquence.';
            }

            if (empty($this->duration)) {
                $this->errors['duration'] = 'Veuillez entrer une durée.';
            } elseif ($this->duration < $minDuration) {
                $this->errors['duration'] = "La durée minimale pour {$this->frequency} est de $minDuration.";
            }

            // Vérifier s'il y a des erreurs
            if (array_filter($this->errors)) {
                return;
            }

            // Calcul des dates
            $startDate = clone $this->serverTime; // Cloner pour éviter de modifier l'original
            $endDate = match ($this->frequency) {
                'quotidienne' => $startDate->addDays($this->duration),
                'hebdomadaire' => $startDate->addWeeks($this->duration),
                'mensuelle' => $startDate->addMonths($this->duration),
            };

            $nbre_depot = $this->duration;
            $frais_gestion = ($nbre_depot * $this->amount) / 30;

            // Création de la tontine
            Tontines::create([
                'nom' => 'Tontine ' . $this->serverTime->format('Y-m-d'),
                'montant_cotisation' => $this->amount,
                'gain_potentiel' => $this->amount * $nbre_depot,
                'frequence' => $this->frequency,
                'date_fin' => $endDate,
                'next_payment_date' => $this->serverTime,
                'frais_gestion' => $frais_gestion,
                'nb_cotisations' => $this->duration,

                'user_id' => Auth::id(),
            ]);

            $this->reset(['amount', 'frequency', 'duration']);
            // Mise à jour des éléments UI
            $this->dispatch('formSubmitted', 'Tontine créée avec succès !');
            $this->tontineStart = !$this->tontineStart;
            $this->dispatch('tontineUpdated', $this->tontineStart);
        } catch (\Exception $e) {
            $this->errors['amount'] = 'Une erreur est survenue lors de la création de la tontine.';
        }
    }

    public function render()
    {
        return view('livewire.tontine');
    }
}
