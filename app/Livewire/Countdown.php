<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect;
use App\Models\Countdown as ModelsCountdown;
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

    /**
     * Initialise le composant avec les données nécessaires.
     *
     * @param int $id
     */
    public function mount($id)
    {
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
                $this->timeRemaining = max(0, $activeCountdown->end_time->diffInSeconds(now()));
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

    /**
     * Démarre un nouveau décompte.
     */
    public function startCountdown()
    {
        // Vérifier qu'il n'y a pas déjà un compte à rebours actif
        if (ModelsCountdown::where('is_active', true)->exists()) {
            return;
        }

        $countdown = ModelsCountdown::create([
            'user_id' => Auth::id(),
            'userSender' => $this->achatdirect->userSender,
            'start_time' => now(),
            'difference' => 'ad',
            'code_unique' => $this->valueCodeUnique,
            'id_achat' => $this->achatdirect->id,
            'time_remaining' => 300,
            'end_time' => now()->addMinutes(5),
            'is_active' => true,
        ]);

        $this->countdownId = $countdown->id;
        $this->isRunning = true;
        $this->timeRemaining = 300;

        // Spécifie explicitement la connexion database
        ProcessCountdown::dispatch($countdown->id)
            ->onConnection('database')
            ->onQueue('default');
        event(new CountdownStarted(300));
        // Spécifie explicitement la connexion database
        ProcessCountdown::dispatch($countdown->id)
            ->onConnection('database')
            ->onQueue('default');
        event(new CountdownStarted(300));
    }

    /**
     * Écouteurs pour les événements diffusés.
     *
     * @return array
     */
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
        if ($this->timeRemaining <= 0) {
            $this->isRunning = false;
            $countdown = ModelsCountdown::find($this->countdownId);
            if ($countdown) {
                $countdown->update([
                    'is_active' => false,
                    'time_remaining' => 0
                ]);
            }
        }
    }

    /**
     * Rend la vue du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.countdown');
    }
}
