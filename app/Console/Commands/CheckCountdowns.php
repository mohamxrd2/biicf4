<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Models\User;
use App\Notifications\AppelOffreTerminerGrouper;
use App\Notifications\CountdownNotificationAd;
use App\Notifications\CountdownNotificationAg;
use App\Notifications\CountdownNotificationAp;
use App\Notifications\NegosTerminer;
use App\Notifications\Confirmation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckCountdowns extends Command
{
    protected $signature = 'check:countdowns';
    protected $description = 'Check countdowns and send notifications if time is up';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(2))
            ->with(['sender', 'achat', 'appelOffre'])
            ->get();

        foreach ($countdowns as $countdown) {
            $this->processCountdown($countdown);
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

                // Nettoyage des données
                $this->cleanUp($codeUnique);
                $countdown->update(['notified' => true]);
            }
        } else {
            Log::warning('Aucun commentaire trouvé pour ce countdown.', ['code_unique' => $codeUnique]);
        }
    }

    private function getLowestPriceComment($codeUnique)
    {
        return Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->orderBy('prixTrade', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();
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
        return $countdown->difference === 'offredirect' ? $highestPriceComment : $lowestPriceComment;
    }

    private function prepareNotificationDetails($countdown, $commentToUse)
    {
        return [
            'code_unique' => $countdown->code_unique,
            'prixTrade' => $commentToUse->prixTrade,
            'livreur' => $commentToUse->id_trader,
            'achat_id' => $countdown->achat->id ?? $countdown->id_achat,
            'id_appeloffre' => $countdown->appelOffre->id ?? $countdown->AppelOffreGrouper_id,
        ];
    }

    private function sendNotificationBasedOnType($countdown, $commentToUse, $details)
    {
        $difference = $countdown->difference;

        switch ($difference) {
            case 'appelOffreGrouper':
                $this->sendGroupedOfferNotification($commentToUse, $details);
                break;

            case 'offredirect':
                $this->sendOffRedirectNotification($commentToUse, $details);
                break;

            case 'grouper':
                $this->sendGroupedNotification($commentToUse, $details);
                break;

            case 'ad':
                $this->sendAdNotification($countdown, $commentToUse, $details);
                break;

            case 'appelOffre':
                $this->sendSingleOfferNotification($countdown, $commentToUse, $details);
                break;


            default:
                Log::warning('Type de notification inconnu.', ['difference' => $difference]);
        }
    }

    private function sendGroupedOfferNotification($commentToUse, $details)
    {
        Notification::send($commentToUse->user, new AppelOffreTerminerGrouper($details));
        Log::info('Notification "appelOffreGrouper" envoyée.', ['user_id' => $commentToUse->user->id]);
    }

    private function sendOffRedirectNotification($commentToUse, $details)
    {
        Notification::send($commentToUse->user, new NegosTerminer($details));
        Log::info('Notification "offredirect" envoyée.', ['user_id' => $commentToUse->user->id]);
    }

    private function sendGroupedNotification($commentToUse, $details)
    {
        Notification::send($commentToUse->user, new AppelOffreTerminerGrouper($details));
    }

    private function sendAdNotification($countdown, $commentToUse, $details)
    {
        $client = User::find($countdown->achat->userSender);

        if ($client) {
            Notification::send($client, new CountdownNotificationAd($details));
        }
    }

    private function sendSingleOfferNotification($countdown, $commentToUse, $details)
    {
        Notification::send($countdown->sender, new CountdownNotificationAp($details));
    }


    private function cleanUp($codeUnique)
    {
        Comment::where('code_unique', $codeUnique)->delete();
        Log::info('Commentaires supprimés.', ['code_unique' => $codeUnique]);
    }
}
