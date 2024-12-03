<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
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
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction(); // Démarre une transaction

        try {

            $countdowns = Countdown::where('notified', false)
                ->where('start_time', '<=', now()->subMinutes(2))
                ->with(['sender', 'achat', 'appelOffre'])
                ->get();

            foreach ($countdowns as $countdown) {
                $this->processCountdown($countdown);
            }

            DB::commit(); // Si tout se passe bien, commit les modifications
        } catch (\Exception $e) {
            DB::rollBack(); // Si une erreur se produit, annule les modifications

            // Enregistrer l'erreur dans les logs
            Log::error('Erreur lors du traitement des countdowns.', ['error' => $e->getMessage()]);
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
            'id' => $countdown->achat->id ?? $countdown->id_achat ?? $countdown->appelOffre->id ?? $countdown->AppelOffreGrouper_id,
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
                $this->sendAdNotification($countdown, $details);
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

    private function sendAdNotification($countdown, $details)
    {

        Notification::send($countdown->sender, new CountdownNotificationAd($details));
        // event(new NotificationSent($countdown->sender));

        // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
        $notification = $countdown->sender->notifications()->where('type', CountdownNotificationAd::class)->latest()->first();

        if ($notification) {
            // Mettez à jour le champ 'type_achat' dans la notification
            $notification->update(['type_achat' => 'Delivery']);
        }

        // Étape 2 : Récupérer le commentaire avec le prix le plus bas
        $lowestPriceComment = $this->getLowestPriceComment($countdown->code_unique); // On passe le code_unique depuis $commentToUse

        // Vérifier si un commentaire avec le prix le plus bas a été trouvé
        if ($lowestPriceComment) {
            // Ajouter les informations supplémentaires (title et description) dans $details
            $details['title'] = 'Négociation terminée';
            $details['description'] = 'Vous venez de gagner la négociation. Voir les détails';

            // Envoyer la notification avec les détails mis à jour
            Notification::send($lowestPriceComment->user, new Confirmation($details));
            // event(new NotificationSent($lowestPriceComment->sender));
        } else {
            Log::warning('Aucun commentaire avec le prix le plus bas trouvé.', [
                'code_unique' => $countdown->code_unique,
                'details' => $details,
            ]);
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
