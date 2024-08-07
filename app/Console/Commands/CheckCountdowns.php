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
        // Récupérer les countdowns non notifiés et dont le start_time est passé depuis au moins une minute
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


            // Vérifier si un enregistrement a été trouvé
            if ($lowestPriceComment) {
                $lowestPrice = $lowestPriceComment->prixTrade;
                $traderId = $lowestPriceComment->id_trader;
                $id_prod = $lowestPriceComment->id_prod;
                $quantiteC = $lowestPriceComment->quantiteC;
                $localite = $lowestPriceComment->localite;
                $specificite = $lowestPriceComment->specificite;
                $nameprod = $lowestPriceComment->nameprod;
                $id_sender = $lowestPriceComment->id_sender;
                $prixProd = $lowestPriceComment->prixProd;

                // Décoder le JSON id_sender
                $decodedSenderIds = json_decode($id_sender, true);

                // Calculer montotal
                $montotal = $quantiteC * $lowestPrice;

                // Définir les détails de la notification
                $details = [
                    'sender_name' => $countdown->sender->id ?? null, // Ajouter le nom de l'expéditeur aux détails de la notification
                    'code_unique' => $countdown->code_unique,
                    'prixTrade' => $lowestPrice,
                    'id_trader' => $traderId,
                    'idProd' => $id_prod,
                    'quantiteC' => $quantiteC,
                    'prixProd' => $prixProd,
                ];
                $Adetails = [
                    'code_unique' => $countdown->code_unique,
                    'prixTrade' => $lowestPrice,
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
                    'prixTrade' => $lowestPrice,
                    'id_trader' => $traderId,
                    'quantiteC' => $quantiteC,
                    'localite' => $localite,
                    'specificite' => $specificite,
                    'nameprod' => $nameprod,
                    'id_sender' => $decodedSenderIds,
                    'montantTotal' => $montotal,
                ];

                // Vérifier si la colonne 'difference' est égale à 'single'
                if ($countdown->difference === 'single') {
                    // Envoyer la notification à l'utilisateur expéditeur
                    Notification::send($lowestPriceComment->user, new AppelOffreTerminer($Adetails));
                } else if ($countdown->difference === 'offredirect') {
                    // Envoyer la notification à l'utilisateur expéditeur
                    Notification::send($lowestPriceComment->user, new NegosTerminer($details));
                } else if ($countdown->difference === 'grouper') {
                    // Envoyer la notification à l'utilisateur expéditeur
                    Notification::send($lowestPriceComment->user, new AppelOffreTerminer($Gdetails));
                } else {
                    // Envoyer une autre notification ou effectuer une autre action
                    Notification::send($countdown->sender, new CountdownNotification($details));
                }
                // Supprimer les commentaires avec ce code unique
                Comment::where('code_unique', $code_unique)->delete();

                // Mettre à jour le statut notified à true
                $countdown->update(['notified' => true]);
            }
        }
    }
}
