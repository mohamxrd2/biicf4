<?php

namespace App\Console\Commands;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\AppelOffreGrouper;
use App\Models\Countdown;
use App\Models\User;
use App\Models\UserQuantites;
use App\Notifications\AppelOffreGrouperNotification;
use App\Notifications\Confirmation;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
    public $countdownId;
    public $isRunning;
    public $timeRemaining;
    public function __construct()
    {
        parent::__construct();
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function handle()
    {

        DB::beginTransaction(); // Démarre une transaction

        try {

            $timeSync = new TimeSyncService($this->recuperationTimer);
            $result = $timeSync->getSynchronizedTime();
            $serverTime = $result['timestamp'];

            $countdowns = Countdown::where('notified', false)
                ->where('end_time', '<=', $serverTime)
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
        try {
            // Actualiser le timer avant de commencer
            $this->timeServer();

            $codeUnique = $appelOffreGroup->codeunique;

            // Vérifier si l'appelOffreGroup est valide
            if (!$appelOffreGroup || !$codeUnique) {
                Log::error('AppelOffreGroup invalide', [
                    'id' => $appelOffreGroup->id ?? 'non défini'
                ]);
                return;
            }
            // Générer un code unique et démarrer le countdown
            $code_livr = $this->generateUniqueReference();

            // Notifications aux utilisateurs
            $this->notifyUsersQuantites($appelOffreGroup, $codeUnique);
            $this->notifyProdUsers($appelOffreGroup, $codeUnique, $code_livr);

            $difference = 'appelOffreGrouper';
            $AppelOffreGrouper_id = $appelOffreGroup->id;


            $this->startCountdown($code_livr, $difference, $AppelOffreGrouper_id, $countdowns);

            // Marquer comme notifié
            $this->markCountdownsAsNotified($countdowns, $codeUnique);
            $this->markAppelOffreAsNotified($appelOffreGroup);
        } catch (\Exception $e) {
            Log::error('Erreur dans processAppelOffreGroup', [
                'appelOffreGroup_id' => $appelOffreGroup->id ?? 'non défini',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    public function startCountdown($code_unique, $difference, $AppelOffreGrouper_id, $countdowns)
    {
        $appelOffreGrouper = AppelOffreGrouper::find($AppelOffreGrouper_id);
        if (!$appelOffreGrouper || !$appelOffreGrouper->user_id) {
            Log::error('Invalid AppelOffreGrouper or missing user_id', ['id' => $AppelOffreGrouper_id]);
            return;
        }

        $countdown = Countdown::firstOrCreate(
            ['code_unique' => $code_unique],
            [
                'user_id' => $appelOffreGrouper->user_id,
                'userSender' => null,
                'start_time' => $this->timestamp,
                'difference' => $difference,
                'AppelOffreGrouper_id' => $AppelOffreGrouper_id,
                'time_remaining' => 120,
                'end_time' => $this->timestamp->addMinutes(2),
                'is_active' => true,
            ]
        );

        if ($countdown->wasRecentlyCreated) {
            $this->countdownId = $countdown->id;
            $this->isRunning = true;
            $this->timeRemaining = 120;

            dispatch(new ProcessCountdown($countdown->id, $code_unique))
                ->onQueue('default')
                ->afterCommit();

            event(new CountdownStarted(120, $code_unique));
        }
    }
    private function markCountdownsAsNotified($countdowns, $codeUnique)
    {
        $countdownsToUpdate = $countdowns->where('code_unique', $codeUnique);
        foreach ($countdownsToUpdate as $countdown) {
            $countdown->update(['notified' => true]);
        }
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }
    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();

        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
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

    private function notifyProdUsers($appelOffreGroup, $codeUnique, $code_livr)
    {
        $prodUsers = $appelOffreGroup->prodUsers;

        if (!$prodUsers) {
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
                    'code_livr' => $code_livr,
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
