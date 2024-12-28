<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect;
use App\Models\AppelOffreGrouper;
use App\Models\AppelOffreUser;
use App\Models\Countdown as ModelsCountdown;
use App\Models\OffreGroupe;
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
    public $appelOffreGroup;
    public $appeloffre;
    public $time;
    public $error;
    public $timestamp;
    public $appeloffregrp;
    public $offgroupe;
    public $etat;
    public $OffreGroupe;

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

        $this->loadNotificationData($id);

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

    private function loadNotificationData($id)
    {
        try {
            $this->notification = DatabaseNotification::findOrFail($id);
            $this->valueCodeUnique = null;

            switch ($this->notification->type) {
                case 'App\Notifications\livraisonAchatdirect':
                    if (isset($this->notification->data['achat_id'])) {
                        $this->achatdirect = AchatDirect::findOrFail($this->notification->data['achat_id']);
                        $this->valueCodeUnique = $this->notification->type_achat === 'appelOffreGrouper'
                            ? ($this->notification->data['code_unique'] ?? null)
                            : $this->achatdirect->code_unique;
                        $this->etat = $this->achatdirect->count;

                        return true;
                    }
                    break;

                case 'App\Notifications\AppelOffre':
                    if (isset($this->notification->data['id_appelOffre'])) {
                        $this->appeloffre = AppelOffreUser::findOrFail($this->notification->data['id_appelOffre']);
                        $this->valueCodeUnique = $this->appeloffre->code_unique;
                        $this->etat = $this->appeloffre->count;

                        return true;
                    }
                    break;

                case 'App\Notifications\OffreNotifGroup':
                    if (isset($this->notification->data['code_unique'])) {
                        $this->offgroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->firstOrFail();
                        $this->valueCodeUnique = $this->offgroupe->code_unique;
                        $this->etat = $this->offgroupe->count;

                        return true;
                    }
                    break;

                case 'App\Notifications\AOGrouper':
                    if (isset($this->notification->data['offre_id'])) {
                        $this->appelOffreGroup = AppelOffreGrouper::findOrFail($this->notification->data['offre_id']);
                        $this->valueCodeUnique = $this->appelOffreGroup->codeunique;
                        $this->etat = $this->appelOffreGroup->count;

                        return true;
                    }
                    break;

                case 'App\Notifications\AppelOffreGrouperNotification':
                    if (isset($this->notification->data['id_appelGrouper'])) {
                        $this->appeloffregrp = AppelOffreGrouper::findOrFail($this->notification->data['id_appelGrouper']);
                        $this->valueCodeUnique = $this->appeloffregrp->codeunique;
                        $this->etat = $this->appeloffregrp->count;

                        return true;
                    }
                    break;
                case 'App\Notifications\OffreNegosNotif':
                    if (isset($this->notification->data['code_unique'])) {
                        $this->OffreGroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->first();
                        $this->valueCodeUnique = $this->OffreGroupe->code_unique;
                        $this->etat = $this->OffreGroupe->count;

                        return true;
                    }
                    break;
            }

            Log::warning('Code unique non trouvé pour la notification', [
                'notification_id' => $id,
                'type' => $this->notification->type
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des données de notification', [
                'notification_id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function updateModelCount()
    {
        try {
            switch ($this->notification->type) {
                case 'App\Notifications\livraisonAchatdirect':
                    $this->achatdirect->update(['count' => true]);
                    $this->dispatch('negotiationEnded')->to('livraison-achatdirect');
                    break;

                case 'App\Notifications\AppelOffre':
                    $this->appeloffre->update(['count' => true]);
                    $this->dispatch('negotiationEnded')->to('appeloffre');
                    break;

                case 'App\Notifications\OffreNotifGroup':
                    $this->offgroupe->update(['count' => true]);
                    $this->dispatch('negotiationEnded')->to('enchere');
                    break;
                case 'App\Notifications\OffreNegosNotif':
                    $this->OffreGroupe->update(['count' => true]);
                    $this->dispatch('negotiationEnded')->to('offre-groupe-quantite');
                    break;
                case 'App\Notifications\AOGrouper':
                    $this->appelOffreGroup->update(['count' => true]);
                    $this->dispatch('negotiationEnded')->to('appel-offre-grouper');
                    break;

                case 'App\Notifications\AppelOffreGrouperNotification':
                    $this->appeloffregrp->update(['count' => true]);
                    $this->dispatch('negotiationEnded')->to('appel-offre-grouper');
                    break;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du modèle', [
                'type' => $this->notification->type,
                'error' => $e->getMessage()
            ]);
            return false;
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

    public function handleCountdownStart($event)
    {
        $this->timeRemaining = $event['time'];
        $this->isRunning = true;
    }


    public function handleCountdownTick($event)
    {
        if (!$this->isRunning) {
            return;
        }

        $this->timeRemaining = $event['time'];

        if ($this->timeRemaining <= 1) {
            $this->isRunning = false;
            $countdown = ModelsCountdown::find($this->countdownId);
            if ($countdown) {
                $countdown->update([
                    'is_active' => false,
                    'time_remaining' => 0
                ]);
            }

            if ($this->loadNotificationData($this->id)) {
                $this->updateModelCount();
            }

            $this->dispatch(
                'formSubmitted',
                'Temps écoulé, Négociation terminée.'
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
