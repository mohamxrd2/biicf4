<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Notifications\AppelOffreTerminer;
use App\Notifications\AppelOffreTerminerGrouper;
use App\Notifications\CountdownNotification;
use App\Notifications\CountdownNotificationAd;
use App\Notifications\CountdownNotificationAp;
use App\Notifications\NegosTerminer;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
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
            ->with('sender') // Charger la relation userSender
            ->get();


        foreach ($countdowns as $countdown) {
            // Récupérer le code unique
            $code_unique = $countdown->code_unique;
            // Log::info('Traitement du countdown.', ['countdown_id' => $countdown->id, 'code_unique' => $code_unique]);

            // Retrouver l'enregistrement avec le prix le plus bas parmi les enregistrements avec ce code unique
            $lowestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'asc')
                ->first();
            // Log::info('Commentaire avec le prix le plus bas récupéré.', ['lowestPriceComment_id' => $lowestPriceComment->id ?? null]);

            // Retrouver l'enregistrement avec le prix le plus élevé parmi les enregistrements avec ce code unique
            $highestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'desc')
                ->first();
            // Log::info('Commentaire avec le prix le plus élevé récupéré.', ['highestPriceComment_id' => $highestPriceComment->id ?? null]);

            // Vérifier si un enregistrement a été trouvé
            if ($lowestPriceComment) {
                $commentToUse = ($countdown->difference === 'offredirect') ? $highestPriceComment : $lowestPriceComment;
                Log::info('Commentaire à utiliser déterminé.', ['commentToUse_id' => $commentToUse->id ?? null]);

                if ($commentToUse) {
                    // Extraire les détails pour la notification
                    Log::info('Préparation des détails de la notification.', ['commentToUse_id' => $commentToUse->id]);

                    $price = $commentToUse->prixTrade;
                    $traderId = $commentToUse->id_trader;
                    $senderId = $commentToUse->id_sender;
                    $id_prod = $commentToUse->id_prod;
                    $quantiteC = $commentToUse->quantiteC;
                    $localite = $commentToUse->localite;
                    $specificite = $commentToUse->specificite;
                    $nameprod = $commentToUse->nameprod;
                    $id_sender = $commentToUse->id_sender;
                    $prixProd = $commentToUse->prixProd;
                    $type = $commentToUse->type;
                    $date_tot = $commentToUse->date_tot;
                    $date_tard = $commentToUse->date_tard;
                    $timeStart = $commentToUse->timeStart;
                    $timeEnd = $commentToUse->timeEnd;
                    $dayPeriod = $commentToUse->dayPeriod;

                    // Décoder le JSON id_sender
                    $decodedSenderIds = json_decode($id_sender, true);
                    $montotal = $quantiteC * $price;

                    // Définir les détails de la notification
                    $details = [
                        'sender_name' => $countdown->sender->id ?? null, // Ajouter le nom de l'expéditeur aux détails de la notification
                        'code_unique' => $countdown->code_unique,
                        'prixTrade' => $price,
                        'fournisseur' => $traderId,
                        'livreur' => $senderId,
                        'idProd' => $id_prod,
                        'quantiteC' => $quantiteC,
                        'prixProd' => $prixProd,
                        'date_tot' => $date_tot,
                        'date_tard' => $date_tard,
                    ];


                    $Gdetails = [
                        'code_unique' => $countdown->code_unique,
                        'prixTrade' => $price,
                        'id_trader' => $traderId,
                        'quantiteC' => $quantiteC,
                        'localite' => $localite,
                        'specificite' => $specificite,
                        'nameprod' => $nameprod,
                        'id_sender' => $decodedSenderIds,
                        'montantTotal' => $montotal,
                        'date_tot' => $date_tot,
                        'date_tard' => $date_tard,
                    ];
                    //lier a apple offre

                    $type_achat = $type ?? null;
                    // Check if a notification with the specific conditions exists
                    $notificationExists = DatabaseNotification::where('type', 'App\Notifications\AppelOffre')
                        ->whereJsonContains('data->code_unique', $code_unique)
                        ->exists();

                    if ($notificationExists) {
                        $notification = DatabaseNotification::where('type', 'App\Notifications\AppelOffre')
                            ->whereJsonContains('data->code_unique', $code_unique)
                            ->first();
                        $notificationData = $notification->data;

                        if (isset($notificationData['reference'])) {
                            $reference = $notificationData['reference'];
                            Log::info('Référence trouvée dans la notification existante.', ['reference' => $reference]);
                        }
                    }
                    // Vérifier le type de notification à envoyer
                    if ($countdown->difference === 'single') {
                        $Adetails = [
                            'code_unique' => $countdown->code_unique,
                            'prixTrade' => $price,
                            'id_trader' => $traderId,
                            'quantiteC' => $quantiteC,
                            'localite' => $localite,
                            'specificite' => $specificite,
                            'nameprod' => $nameprod,
                            'id_sender' => $decodedSenderIds,
                            'montantTotal' => $montotal,
                            'reference' => $reference,
                            'date_tot' => $date_tot,
                            'date_tard' => $date_tard,
                            'timeStart' => $timeStart ?? null,
                            'timeEnd' => $timeEnd ?? null,
                            'dayPeriod' => $dayPeriod ?? null,
                        ];
                        Log::info('Envoi de la notification pour type "single".', ['user_id' => $commentToUse->user->id]);
                        Notification::send($commentToUse->user, new AppelOffreTerminer($Adetails));

                        $notification = $commentToUse->user->notifications()->where('type', AppelOffreTerminer::class)->latest()->first();
                        if ($notification) {
                            Log::info('Mise à jour de la notification existante.', ['notification_id' => $notification->id]);
                            $notification->update(['type_achat' => $type_achat]);
                        }
                    } else if ($countdown->difference === 'offredirect') {
                        Log::info('Envoi de la notification pour type "offredirect".', ['user_id' => $lowestPriceComment->user->id]);
                        Notification::send($lowestPriceComment->user, new NegosTerminer($details));
                    } else if ($countdown->difference === 'grouper') {
                        Log::info('Envoi de la notification pour type "grouper".', ['user_id' => $lowestPriceComment->user->id]);
                        Notification::send($lowestPriceComment->user, new AppelOffreTerminerGrouper($Gdetails));

                        $notification = $lowestPriceComment->user->notifications()->where('type', AppelOffreTerminer::class)->latest()->first();
                        if ($notification) {
                            Log::info('Mise à jour de la notification existante.', ['notification_id' => $notification->id]);
                            $notification->update(['type_achat' => 'OFG']);
                        }
                    } else  if ($countdown->difference === 'ad') {
                        Log::info('Envoi d\'une autre notification ou action par défaut.');
                        Notification::send($countdown->sender, new CountdownNotificationAd($details));

                    } else  if ($countdown->difference === 'ap') {
                        Log::info('Envoi d\'une autre notification ou action par défaut.');
                        Notification::send($countdown->sender, new CountdownNotificationAp($details));

                    } else {
                        Log::info('Envoi d\'une autre notification ou action par défaut.');
                        Notification::send($countdown->sender, new CountdownNotification($details));
                    }

                    // Supprimer les commentaires avec ce code unique
                    Log::info('Suppression des commentaires avec code_unique.', ['code_unique' => $code_unique]);
                    Comment::where('code_unique', $code_unique)->delete();

                    // $notificationExists = DatabaseNotification::where(function ($query) use ($code_unique) {
                    //     $query->where('type', 'App\Notifications\livraisonVerif')
                    //         ->orWhere('type', 'App\Notifications\AppelOffreGrouperNotification')
                    //         ->orWhere('type', 'App\Notifications\AppelOffre');
                    // })
                    //     ->whereJsonContains('data->code_livr', $code_unique)
                    //     ->exists();

                    // if ($notificationExists) {
                    //     Log::info('Suppression des notifications existantes contenant le code_unique.', ['code_unique' => $code_unique]);
                    //     DatabaseNotification::whereJsonContains('data->code_livr', $code_unique)->delete();
                    //     DatabaseNotification::whereJsonContains('data->code_unique', $code_unique)->delete();
                    // }

                    // // Mettre à jour le statut notified à true
                    // Log::info('Mise à jour du statut "notified" à true.', ['countdown_id' => $countdown->id]);
                    $countdown->update(['notified' => true]);
                }
            }
        }
    }
}
