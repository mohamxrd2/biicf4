<?php

namespace App\Livewire;

use App\Models\Tontines;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use App\Services\Tontine\TontineCalculationService;
use App\Services\Tontine\TontineValidationService;
use App\Services\Tontine\TontineCreationService;
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
    public $tontineEnCours = null;
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

        $this->tontineStart = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', $this->serverTime)
            ->exists();

        $this->tontineDatas = Tontines::where('user_id', Auth::id())
            ->where('date_fin', '>=', $this->serverTime)
            ->get();

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

        $this->errors = $this->validationService->validateTontine([
            'amount' => $this->amount,
            'frequency' => $this->frequency,
            'duration' => $this->duration
        ]);

        if (array_filter($this->errors)) {
            return;
        }

        $success = $this->creationService->createTontine([
            'amount' => $this->amount,
            'frequency' => $this->frequency,
            'duration' => $this->duration,
            'server_time' => $this->serverTime
        ], Auth::id());

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
        return view('livewire.tontine');
    }
}
