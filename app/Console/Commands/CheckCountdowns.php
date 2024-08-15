<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Notifications\AppelOffreTerminer;
use App\Notifications\CountdownNotification;
use App\Notifications\NegosTerminer;
use Illuminate\Console\Command;
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

        foreach ($countdowns as $countdown) {
            // Récupérer le code unique
            $code_unique = $countdown->code_unique;

            // Retrouver l'enregistrement avec le prix le plus bas parmi les enregistrements avec ce code unique
            $lowestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'asc')
                ->first();

            // Retrouver l'enregistrement avec le prix le plus élevé parmi les enregistrements avec ce code unique
            $highestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'desc')
                ->first();

            // Vérifier si un enregistrement a été trouvé
            if ($lowestPriceComment) {
                // Déterminer quel commentaire utiliser en fonction de la différence
                $commentToUse = ($countdown->difference === 'offredirect') ? $highestPriceComment : $lowestPriceComment;

                if ($commentToUse) {
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

                    // Calculer montotal
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
                        // Envoyer la notification avec le prix le plus élevé
                        Notification::send($commentToUse->user, new AppelOffreTerminer($Adetails));
                    } else if ($countdown->difference === 'offredirect') {
                        // Envoyer la notification avec le prix le plus bas
                        Notification::send($lowestPriceComment->user, new NegosTerminer($details));
                    } else if ($countdown->difference === 'grouper') {
                        // Envoyer la notification avec le prix le plus bas
                        Notification::send($lowestPriceComment->user, new AppelOffreTerminer($Gdetails));

                        // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
                        $notification = $lowestPriceComment->user->notifications()->where('type', AppelOffreTerminer::class)->latest()->first();

                        if ($notification) {
                            // Mettez à jour le champ 'type_achat' dans la notification
                            $notification->update(['type_achat' => 'OFG']);
                        }
                    } else {
                        // Envoyer une autre notification ou effectuer une autre action
                        Notification::send($countdown->sender, new CountdownNotification($details));
                    }

                    // Supprimer les commentaires avec ce code unique
                    Comment::where('code_unique', $code_unique)->delete();

                    // Vérifiez si une notification avec le type 'livraison' et le code unique existe
                    $notificationExists = Notification::where('type', 'App\Notifications\livraisonVerif')
                        ->whereJsonContains('data->code_livr', $code_unique)
                        ->exists();

                    if ($notificationExists) {
                        // Supprimez toutes les notifications qui contiennent ce code unique dans 'data'
                        Notification::whereJsonContains('data->code_livr', $code_unique)
                            ->delete();
                    }

                    // Mettre à jour le statut notified à true
                    $countdown->update(['notified' => true]);
                }
            }
        }
    }
}
