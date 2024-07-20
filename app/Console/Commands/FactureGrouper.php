<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Notifications\Facturegrouper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class FactureGrouper extends Command
{
    protected $signature = 'facture:proformat';
    protected $description = 'Check facture grouper';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Récupérer les countdowns non notifiés et dont le start_time est passé depuis au moins une minute
        $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(1))
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

                // Définir les détails de la notification
                $details = [
                    'code_unique' => $code_unique,
                    'prixTrade' => $lowestPrice,
                    'id_trader' => $traderId,
                    'idProd' => $id_prod,
                    'quantiteC' => $quantiteC,
                ];

                // Décoder et traiter les utilisateurs associés
                $decodedProdUsers = json_decode($countdown->nsender, true);
                if (is_array($decodedProdUsers)) {
                    foreach ($decodedProdUsers as $prodUser) {
                        $owner = User::find($prodUser);
                        if ($owner) {
                            Notification::send($owner, new Facturegrouper($details));
                        }
                    }
                }

                // Mettre à jour le statut notified à true
                $countdown->update(['notified' => true]);
            }
        }
    
    }
}

