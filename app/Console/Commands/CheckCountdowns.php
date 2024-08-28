<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Notifications\AppelOffreTerminer;
use App\Notifications\CountdownNotification;
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
            ->where('start_time', '<=', now()->subMinutes(1))
            ->with('sender') // Charger la relation userSender
            ->get();

        Log::info('Début du traitement des countdowns.', ['countdowns_count' => $countdowns->count()]);

        foreach ($countdowns as $countdown) {
            // Récupérer le code unique
            $code_unique = $countdown->code_unique;
            Log::info('Traitement du countdown.', ['countdown_id' => $countdown->id, 'code_unique' => $code_unique]);

            // Retrouver l'enregistrement avec le prix le plus bas parmi les enregistrements avec ce code unique
            $lowestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'asc')
                ->first();
            Log::info('Commentaire avec le prix le plus bas récupéré.', ['lowestPriceComment_id' => $lowestPriceComment->id ?? null]);

            // Retrouver l'enregistrement avec le prix le plus élevé parmi les enregistrements avec ce code unique
            $highestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'desc')
                ->first();
            Log::info('Commentaire avec le prix le plus élevé récupéré.', ['highestPriceComment_id' => $highestPriceComment->id ?? null]);

            // Vérifier si un enregistrement a été trouvé
            if ($lowestPriceComment) {
                $commentToUse = ($countdown->difference === 'offredirect') ? $highestPriceComment : $lowestPriceComment;
                Log::info('Commentaire à utiliser déterminé.', ['commentToUse_id' => $commentToUse->id ?? null]);

                if ($commentToUse) {
                    // Extraire les détails pour la notification
                    Log::info('Préparation des détails de la notification.', ['commentToUse_id' => $commentToUse->id]);

                    $price = $commentToUse->prixTrade;
                    $traderId = $commentToUse->id_trader;
                    $id_prod = $commentToUse->id_prod;
                    $quantiteC = $commentToUse->quantiteC;
                    $localite = $commentToUse->localite;
                    $specificite = $commentToUse->specificite;
                    $nameprod = $commentToUse->nameprod;
                    $id_sender = $commentToUse->id_sender;
                    $prixProd = $commentToUse->prixProd;

                    // Décoder le JSON id_sender
                    $decodedSenderIds = json_decode($id_sender, true);
                    $montotal = $quantiteC * $price;

                    // Définir les détails de la notification
                    $details = [
                        'sender_name' => $countdown->sender->id ?? null, // Ajouter le nom de l'expéditeur aux détails de la notification
                        'code_unique' => $countdown->code_unique,
                        'prixTrade' => $price,
                        'id_trader' => $traderId,
                        'idProd' => $id_prod,
                        'quantiteC' => $quantiteC,
                        'prixProd' => $prixProd,
                    ];

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
                    ];

                    // Vérifier le type de notification à envoyer
                    if ($countdown->difference === 'single') {
                        Log::info('Envoi de la notification pour type "single".', ['user_id' => $commentToUse->user->id]);
                        Notification::send($commentToUse->user, new AppelOffreTerminer($Adetails));
                    } else if ($countdown->difference === 'offredirect') {
                        Log::info('Envoi de la notification pour type "offredirect".', ['user_id' => $lowestPriceComment->user->id]);
                        Notification::send($lowestPriceComment->user, new NegosTerminer($details));
                    } else if ($countdown->difference === 'grouper') {
                        Log::info('Envoi de la notification pour type "grouper".', ['user_id' => $lowestPriceComment->user->id]);
                        Notification::send($lowestPriceComment->user, new AppelOffreTerminer($Gdetails));

                        $notification = $lowestPriceComment->user->notifications()->where('type', AppelOffreTerminer::class)->latest()->first();
                        if ($notification) {
                            Log::info('Mise à jour de la notification existante.', ['notification_id' => $notification->id]);
                            $notification->update(['type_achat' => 'OFG']);
                        }
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
            } else {
                Log::warning('Aucun commentaire trouvé pour le code_unique.', ['code_unique' => $code_unique]);
            }
        }
    }
}
Log::info('Fin du traitement des countdowns.');
