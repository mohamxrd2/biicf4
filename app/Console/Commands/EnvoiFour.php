<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Notifications\AppelOffreTerminer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EnvoiFour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:envoi-four';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
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

                // Décoder le JSON id_sender
                $decodedSenderIds = json_decode($id_sender, true);

                // Calculer montotal
                $montotal = $quantiteC * $lowestPrice;

                // Définir les détails de la notification

                


                // Vérifier si la colonne 'difference' est égale à 'single'
                if ($countdown->difference === 'grouper') {
                    // Envoyer la notification à l'utilisateur expéditeur
                    Notification::send($lowestPriceComment->user, new AppelOffreTerminer($Gdetails));
                }

                // Mettre à jour le statut notified à true
                $countdown->update(['notified' => true]);
            }
        }
    }
}
