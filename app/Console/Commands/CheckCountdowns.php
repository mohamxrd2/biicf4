<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\User;
use App\Notifications\AppelOffreTerminer;
use App\Notifications\AppelOffreTerminerGrouper;
use App\Notifications\CountdownNotificationAd;
use App\Notifications\NegosTerminer;
use App\Notifications\Confirmation;
use App\Services\TimeSync\TimeSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Services\RecuperationTimer;
use Carbon\Carbon;

class CheckCountdowns extends Command
{
    protected $signature = 'check:countdowns';
    protected $description = 'Check countdowns and send notifications if time is up';
    private $recuperationTimer;
    public $time;
    public $error;
    public $timestamp;

    public function __construct(RecuperationTimer $recuperationTimer)
    {
        parent::__construct();
        $this->recuperationTimer = $recuperationTimer;
    }

    public function handle()
    {
        DB::beginTransaction();

        try {
            // Utiliser TimeSyncService pour l'heure du serveur
            $timeSync = new TimeSyncService($this->recuperationTimer);
            $result = $timeSync->getSynchronizedTime();
            $serverTime = $result['timestamp'];

            $countdowns = Countdown::where('notified', false)
                ->where('end_time', '<=', $serverTime)
                ->with(['sender', 'achat', 'appelOffre'])
                ->get();

            foreach ($countdowns as $countdown) {

                $this->processCountdown($countdown);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement des countdowns.', [
                'error' => $e->getMessage(),
                'server_time' => $serverTime ?? null
            ]);
        }
    }

    private function processCountdown($countdown)
    {
        $codeUnique = $countdown->code_unique;

        // Récupération des commentaires
        $lowestPriceComment = $this->getLowestPriceComment($codeUnique);
        $highestPriceComment = $this->getHighestPriceComment($codeUnique);

        if ($lowestPriceComment) {
            $commentToUse = $this->determineCommentToUse($countdown, $lowestPriceComment, $highestPriceComment);

            if ($commentToUse) {
                // Préparer les détails de notification
                $details = $this->prepareNotificationDetails($countdown, $commentToUse);

                // Envoyer la notification en fonction du type
                $this->sendNotificationBasedOnType($countdown, $commentToUse, $details);

                $countdown->update(['notified' => true]);
                // Nettoyage des données
                // $this->cleanUp($codeUnique);
            }
        };
    }

    private function getLowestPriceComment($codeUnique)
    {
        return Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->orderBy('prixTrade', 'asc')
            ->orderBy('created_at', 'asc')
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
    private function getHighestPriceComment($codeUnique)
    {
        return Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->orderBy('prixTrade', 'desc')
            ->first();
    }

    private function determineCommentToUse($countdown, $lowestPriceComment, $highestPriceComment)
    {
        return $countdown->difference === 'enchere' ? $highestPriceComment : $lowestPriceComment;
    }

    private function prepareNotificationDetails($countdown, $commentToUse)
    {
        return [
            'code_unique' => $countdown->code_unique,
            'prixTrade' => $commentToUse->prixTrade,
            'livreur' => $commentToUse->id_trader,
            'id' => optional($countdown->achat)->id
                ?? $countdown->id_achat
                ?? optional($countdown->appelOffre)->id
                ?? $countdown->AppelOffreGrouper_id,
        ];
    }

    private function sendNotificationBasedOnType($countdown, $commentToUse, $details)
    {
        try {
            $difference = $countdown->difference;

            if (!$commentToUse || !$commentToUse->user) {
                Log::warning('Commentaire ou utilisateur manquant pour la notification', [
                    'countdown_id' => $countdown->id,
                    'difference' => $difference
                ]);
                return;
            }

            switch ($difference) {
                case 'appelOffreGrouper':
                    $this->sendGroupedOfferNotification($commentToUse, $details);
                    break;

                case 'enchere':
                    $this->sendEnchereNotification($commentToUse, $details);
                    break;

                case 'ad':
                    $this->sendAdNotification($countdown, $details);
                    break;
                case 'AchatDirectPoffreGroupe':
                    $this->sendADPoffreGroupeNotification($countdown, $details);
                    break;

                case 'appelOffreD':
                case 'appelOffreR':
                    $this->sendSingleOfferNotification($countdown, $commentToUse, $details);
                    break;

                default:
                    Log::warning('Type de notification inconnu.', [
                        'difference' => $difference,
                        'countdown_id' => $countdown->id
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function sendGroupedOfferNotification($commentToUse, $details)
    {
        try {
            if (!$commentToUse->user) {
                Log::error('User not found for comment');
                return;
            }

            Notification::send($commentToUse->user, new AppelOffreTerminerGrouper($details));
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }

    private function sendEnchereNotification($commentToUse, $details)
    {
        $details['title'] = 'Négociation terminée';
        $details['description'] = 'Vous venez de gagner la négociation. Voir les détails';
        $details['id_trader'] = $commentToUse->id_trader ?? null;
        $details['idProd'] = $commentToUse->id_prod ?? null;

        Notification::send($commentToUse->user, new NegosTerminer($details));
    }


    private function sendAdNotification($countdown, $details)
    {
        try {
            // Envoyer la notification au sender
            if ($countdown->sender) {
                $details['type_achat'] = 'Delivery';

                Notification::send($countdown->sender, new CountdownNotificationAd($details));
                event(new NotificationSent($countdown->sender));
            }

            // Récupérer le meilleur prix avec orderBy sur created_at
            $lowestPriceComment = Comment::where('code_unique', $countdown->code_unique)
                ->orderBy('prixTrade', 'asc')
                ->orderBy('created_at', 'asc')  // En cas d'égalité de prix, prendre le premier
                ->with('user')  // Charger la relation user
                ->first();

            if ($lowestPriceComment && $lowestPriceComment->user) {
                $details['title'] = 'Négociation terminée';
                $details['description'] = 'Vous venez de gagner la négociation. Voir les détails';
                $details['prixTrade'] = $lowestPriceComment->prixTrade;
                $details['id_trader'] = $lowestPriceComment->id_trader;

                Notification::send($lowestPriceComment->user, new Confirmation($details));
                event(new NotificationSent($lowestPriceComment->user));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'countdown_id' => $countdown->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    private function sendADPoffreGroupeNotification($countdown, $details)
    {
        try {
            // Envoyer la notification au sender
            if ($countdown->sender) {

                Notification::send($countdown->sender, new CountdownNotificationAd($details));
                event(new NotificationSent($countdown->sender));
            }

            // Récupérer le meilleur prix avec orderBy sur created_at
            $lowestPriceComment = Comment::where('code_unique', $countdown->code_unique)
                ->orderBy('prixTrade', 'asc')
                ->orderBy('created_at', 'asc')  // En cas d'égalité de prix, prendre le premier
                ->with('user')  // Charger la relation user
                ->first();

            if ($lowestPriceComment && $lowestPriceComment->user) {
                $details['title'] = 'Négociation terminée';
                $details['description'] = 'Vous venez de gagner la négociation. Voir les détails';
                $details['prixTrade'] = $lowestPriceComment->prixTrade;
                $details['id_trader'] = $lowestPriceComment->id_trader;

                Notification::send($lowestPriceComment->user, new Confirmation($details));
                event(new NotificationSent($lowestPriceComment->user));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'countdown_id' => $countdown->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendSingleOfferNotification($countdown, $commentToUse, $details)
    {
        $details['id_trader'] = $commentToUse->id_trader ?? null;

        switch ($countdown->difference) {
            case 'appelOffreD':
                $details['type_achat'] = 'Delivery';
                break;

            case 'appelOffreR':
                $details['type_achat'] = 'Take Away';
                break;
        }

        Notification::send($commentToUse->user, new AppelOffreTerminer($details));
    }


    private function cleanUp($codeUnique)
    {
        Comment::where('code_unique', $codeUnique)->delete();
    }
}
