<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect;
use App\Models\Countdown as ModelsCountdown;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Countdown extends Component
{
    public $notification;
    public $id;
    public $timeRemaining = 0;
    public $isRunning = false;
    public $countdownId = null;
    public $achatdirect;
    public $valueCodeUnique;
    public $time;
    public $error;
    public $timestamp;

    protected $recuperationTimer;

    // Injection de la classe RecuperationTimer via le constructe
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }
    public function mount($id)
    {

        // Actualiser le timer avant de commencer
        $this->timeServer();

        $this->notification = DatabaseNotification::findOrFail($id);

        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);

        // Déterminer la valeur de $valueCodeUnique
        $this->valueCodeUnique = $this->notification->type_achat === 'appelOffreGrouper'
            ? ($this->notification->data['code_unique'] ?? null)
            : $this->achatdirect->code_unique;

        // Récupérer le décompte actif
        $activeCountdown = ModelsCountdown::where('code_unique', $this->valueCodeUnique)
            ->where('notified', false)
            ->whereNotNull('start_time')
            ->where('is_active', true)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($activeCountdown) {
            // Vérifiez si 'end_time' est défini et non null
            if ($activeCountdown->end_time) {
                $this->timeRemaining = max(0, $activeCountdown->end_time->diffInSeconds($this->timestamp));
            } else {
                // Définir un temps restant par défaut si 'end_time' est null
                $this->timeRemaining = 0;
                Log::warning("Le champ 'end_time' est null pour le countdown ID: {$activeCountdown->id}");
            }
            $this->isRunning = $this->timeRemaining > 0;
        } else {
            // Temps par défaut si aucun décompte actif
            $this->timeRemaining = 300;
        }
    }

    public function timeServer()
    {
        // Faire plusieurs tentatives de récupération pour plus de précision
        $attempts = 3;
        $times = [];

        for ($i = 0; $i < $attempts; $i++) {
            // Récupération de l'heure via le service
            $currentTime = $this->recuperationTimer->getTime();
            if ($currentTime) {
                $times[] = $currentTime;
            }
            // Petit délai entre chaque tentative
            usleep(50000); // 50ms
        }

        if (empty($times)) {
            // Si aucune tentative n'a réussi, utiliser l'heure système
            $this->error = "Impossible de synchroniser l'heure. Utilisation de l'heure système.";
            $this->time = now()->timestamp * 1000;
        } else {
            // Utiliser la médiane des temps récupérés pour plus de précision
            sort($times);
            $medianIndex = floor(count($times) / 2);
            $this->time = $times[$medianIndex];
            $this->error = null;
        }

        // Convertir en secondes
        $seconds = intval($this->time / 1000);
        // Créer un objet Carbon pour le timestamp
        $this->timestamp = Carbon::createFromTimestamp($seconds);

        // Log pour debug
        Log::info('Timer actualisé', [
            'timestamp' => $this->timestamp,
            'time_ms' => $this->time,
            'attempts' => count($times)
        ]);
    }
    public function getListeners()
    {
        return [
            "echo:countdown,CountdownStarted" => 'handleCountdownStart',
            "echo:countdown,CountdownTick" => 'handleCountdownTick',
        ];
    }

    /**
     * Gère l'événement CountdownStarted.
     *
     * @param array $event
     */
    public function handleCountdownStart($event)
    {
        $this->timeRemaining = $event['time'];
        $this->isRunning = true;
    }

    /**
     * Gère l'événement CountdownTick.
     *
     * @param array $event
     */
    public function handleCountdownTick($event)
    {
        // Vérifier si le compte à rebours est actif
        if (!$this->isRunning) {
            return;
        }

        $this->timeRemaining = $event['time'];

        // Mettre à jour l'état
        if ($this->timeRemaining <= 1) {
            $this->isRunning = false;
            $countdown = ModelsCountdown::find($this->countdownId);
            if ($countdown) {
                $countdown->update([
                    'is_active' => false,
                    'time_remaining' => 0
                ]);
            }

            // Mettre à jour l'attribut 'finish' du demandeCredit
            $this->achatdirect->update([
                'count' => true,

            ]);

            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Négociation terminé.'
            );
        }
    }


    public function render()
    {

        return view('livewire.countdown', [
            'timeRemaining' => $this->timeRemaining,
        ]);
    }
}
