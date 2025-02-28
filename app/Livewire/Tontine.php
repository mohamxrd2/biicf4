<?php

namespace App\Livewire;

use App\Models\Cotisation;
use App\Models\Tontines;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use App\Services\Tontine\TontineCalculationService;
use App\Services\Tontine\TontineValidationService;
use App\Services\Tontine\TontineCreationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class Tontine extends Component
{
    public $amount;
    public $frequency;
    public $duration;
    public $tontineStart = false;
    public $tontineDatas;
    public $cotisations;
    public $cotisationsCount;
    public $cotisationSum;
    public $pourcentage;
    public $tontineEnCours;
    public $serverTime;
    public $time;
    public $error;
    public $timestamp;
    public $startDay;
    public $errors = [
        'amount' => '',
        'frequency' => '',
        'duration' => ''
    ];
    public $isUnlimited = false;

    private $calculationService;
    private $validationService;
    private $creationService;
    private $recuperationTimer;

    public function boot(
        TontineCalculationService $calculationService,
        TontineValidationService $validationService,
        TontineCreationService $creationService
    ) {
        $this->calculationService = $calculationService;
        $this->validationService = $validationService;
        $this->creationService = $creationService;
        $this->recuperationTimer = new RecuperationTimer();
    }


    public function mount()
    {
        $this->resetErrors();
        $this->timeServer();
        $this->serverTime = $this->timestamp;

        // Récupérer la tontine active de l'utilisateur
        $this->tontineEnCours = Tontines::where('user_id', Auth::id())
            ->whereIn('statut', ['active', '1st'])
            ->first();


        $this->tontineStart = $this->tontineEnCours !== null;

        // Récupérer toutes les tontines de l'utilisateur
        $this->tontineDatas = Tontines::where('user_id', Auth::id())
            ->where('statut', 'inactive')
            ->get()
            ->map(function ($tontine) {
                // Récupération des cotisations réussies
                $cotisationsReussies = Cotisation::where('tontine_id', $tontine->id)
                    ->where('statut', 'payé')
                    ->get();

                // Calculs des valeurs nécessaires
                $cts_reussi = $cotisationsReussies->count();
                $cts_sum = $cotisationsReussies->sum('montant');
                $nombreCotisations = $tontine->nombre_cotisations ?: 1;
                $pourcentage = ($cts_reussi / $nombreCotisations) * 100;

                // Ajouter les valeurs calculées à l'objet tontine
                $tontine->cts_reussi = $cts_reussi;
                $tontine->cts_sum = $cts_sum;
                $tontine->pourcentage = round($pourcentage, 2);

                return $tontine;
            });


        //Vérifier si une tontine active existe avant d'accéder à ses cotisations
        if ($this->tontineEnCours) {
            $this->cotisations = Cotisation::where('user_id', Auth::id())
                ->where('tontine_id', $this->tontineEnCours->id)
                ->get();

            $this->cotisationsCount = $this->cotisations->where('statut', 'payé')->count();
            $this->cotisationSum = $this->cotisations->sum('montant');

            // Éviter la division par zéro
            $nombreCotisations = $this->tontineEnCours->nombre_cotisations ?: 1;
            $this->pourcentage = ($this->cotisationsCount / $nombreCotisations) * 100;

            // Calculate percentage differently for unlimited tontines
            if ($this->tontineEnCours->isUnlimited) {
            } else {
            }
        } else {
            // Initialiser les valeurs si aucune tontine active n'est trouvée
            $this->cotisations = collect(); // Collection vide
            $this->cotisationsCount = 0;
            $this->cotisationSum = 0;
            $this->pourcentage = 0;
        }

        $this->changing();
    }


    #[On('tontineUpdated')]
    public function changing()
    {
        $this->tontineEnCours = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', $this->serverTime)
            ->first();
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
            $calculations = $this->calculationService->calculatePotentialGain($this->amount, $this->duration);
            $endDate = $this->calculationService->calculateEndDate($this->serverTime, $this->frequency, $this->duration);

            $this->dispatch('updateCalculations', [
                'potentialGain' => number_format($calculations['montant_total'], 0, '.', ' ') . ' FCFA',
                'fraisDeService' => number_format($calculations['frais_gestion'], 0, '.', ' ') . ' FCFA',
                'endDate' => $endDate->format('d/m/Y')
            ]);
        }
    }

    public function initiateTontine()
    {
        $this->resetErrors();

        // Ajustement de la durée si isUnlimited est activé
        $duration = $this->isUnlimited ? $this->calculationService->getMinDuration($this->frequency) : $this->duration;

        $this->errors = $this->validationService->validateTontine([
            'amount' => $this->amount,
            'frequency' => $this->frequency,
            'duration' => $duration
        ], $this->isUnlimited);

        if (array_filter($this->errors)) {
            return;
        }

        $success = $this->creationService->createTontine([
            'amount' => $this->amount,
            'frequency' => $this->frequency,
            'duration' => $duration,
            'server_time' => $this->serverTime
        ], Auth::id(), $this->isUnlimited);

        if ($success) {
            $this->reset(['amount', 'frequency', 'duration']);
            $this->dispatch('formSubmitted', 'Tontine créée avec succès !');
            $this->tontineStart = !$this->tontineStart;
            $this->dispatch('tontineUpdated');
        } else {
            session()->flash('error', 'Une erreur est survenue ou Solde insuffisant pour créer la tontine.');
        }
    }


    public function render()
    {
        $this->tontineEnCours = Tontines::where('user_id', Auth::id())
            ->whereIn('statut', ['active', '1st'])
            ->first();

        return view('livewire.tontine', [
            'tontineEnCours' => $this->tontineEnCours,
        ]);
    }
}
