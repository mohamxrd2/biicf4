<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Models\AppelOffreGrouper;
use App\Models\Countdown;
use App\Models\User;
use App\Models\UserQuantites;
use App\Notifications\AppelOffreGrouperNotification;
use App\Notifications\Confirmation;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AppeloffreCountdown extends Command
{
    protected $signature = 'app:appeloffreGrouper';
    protected $description = 'Gère les notifications pour les appels d\'offres soumis';
    protected $recuperationTimer;
    public $time;
    public $error;
    public $timestamp;
    public function __construct()
    {
        parent::__construct();
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function handle()
    {
        $this->time = $this->recuperationTimer->getTime();

        DB::beginTransaction(); // Démarre une transaction

        try {
            $countdowns = Countdown::where('notified', false)
                ->where('start_time', '<=', now()->subMinutes(2))
                ->where('difference', 'quantiteGrouper')
                ->with(['sender', 'achat', 'appelOffre', 'appelOffreGrouper'])
                ->get();

            // Récupérer tous les codes uniques des Countdown non notifiés
            $codeUniques = $countdowns->pluck('code_unique')->unique();

            // Récupérer les AppelOffreGrouper correspondants
            $appelsOffreGroups = AppelOffreGrouper::whereIn('codeunique', $codeUniques)->get();

            // Traiter chaque AppelOffreGroup
            foreach ($appelsOffreGroups as $appelOffreGroup) {
                $this->processAppelOffreGroup($appelOffreGroup, $countdowns);
            }

            DB::commit(); // Si tout se passe bien, commit les modifications
        } catch (\Exception $e) {
            DB::rollBack(); // Si une erreur se produit, annule les modifications

            // Enregistrer l'erreur dans les logs
            Log::error('Erreur lors du traitement des countdowns.', ['error' => $e->getMessage()]);
        }
    }

    private function processAppelOffreGroup($appelOffreGroup, $countdowns)
    {
        // Actualiser le timer avant de commencer
        $this->timeServer();

        $codeUnique = $appelOffreGroup->codeunique;

        // Notifications aux utilisateurs
        $this->notifyUsersQuantites($appelOffreGroup, $codeUnique);
        $this->notifyProdUsers($appelOffreGroup, $codeUnique);

        // Créer un nouveau countdown pour l'AppelOffreGroup
        Countdown::create([
            'user_id' => $countdowns->where('code_unique', $codeUnique)->pluck('user_id')->unique()->first(),
            'userSender' => null,
            'start_time' => $this->timestamp,
            'code_unique' => $codeUnique,
            'difference' => 'appelOffreGrouper',
            'AppelOffreGrouper_id' => $appelOffreGroup->id,
        ]);

        // Marquer les Countdown liés comme notifiés
        $this->markCountdownsAsNotified($countdowns, $codeUnique);
    }

    private function markCountdownsAsNotified($countdowns, $codeUnique)
    {
        $countdownsToUpdate = $countdowns->where('code_unique', $codeUnique);
        foreach ($countdownsToUpdate as $countdown) {
            $countdown->update(['notified' => true]);
        }

        Log::info('Countdowns marqués comme notifiés:', $countdownsToUpdate->toArray());
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
    private function notifyUsersQuantites($appelOffreGroup, $codeUnique)
    {

        $userQuantites = userquantites::where('code_unique', $codeUnique)->get();
        foreach ($userQuantites as $userQuantite) {
            $user = User::find($userQuantite->user_id);

            if ($user) {
                $achatUser = [
                    'id' => $appelOffreGroup->id,
                    'idProd' => $appelOffreGroup->id_prod,
                    'code_unique' => $codeUnique,
                    'title' => 'Confirmation de commande',
                    'description' => 'Votre commande a été envoyée avec succès.',
                ];

                Notification::send($user, new Confirmation($achatUser));
                event(new NotificationSent($user));
            }
        }
    }

    private function notifyProdUsers($appelOffreGroup, $codeUnique)
    {
        $prodUsers = $appelOffreGroup->prodUsers;

        if (!$prodUsers) {
            Log::warning('Aucun prodUser trouvé pour cet appel d\'offre', [
                'code_unique' => $codeUnique,
            ]);
            return;
        }

        $decodedProdUsers = json_decode($prodUsers, true) ?? [];
        $totalPersonnes = count($decodedProdUsers);

        foreach ($decodedProdUsers as $prodUserId) {
            $owner = User::find($prodUserId);

            if ($owner) {
                $data = [
                    'id_appelGrouper' => $appelOffreGroup->id,
                    'totalPersonnes' => $totalPersonnes,
                    'code_unique' => $codeUnique,
                    'title' => 'Négociation d\'une commande groupée',
                    'description' => 'Cliquez pour participer à la négociation.',
                ];

                Notification::send($owner, new AppelOffreGrouperNotification($data));
                event(new NotificationSent($owner));
            }
        }
    }

    private function markAppelOffreAsNotified($appelOffreGroup)
    {
        $appelOffreGroup->update(['notified' => true]);
    }
}
