<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Models\User;
use App\Notifications\FacturegrouperNotifications;
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

            if ($lowestPriceComment) {
                $lowestPrice = $lowestPriceComment->prixTrade;
                $traderId = $lowestPriceComment->id_trader;
                $id_prod = $lowestPriceComment->id_prod;
                $quantiteC = $lowestPriceComment->quantiteC;

                $details = [
                    'code_unique' => $code_unique,
                    'prixTrade' => $lowestPrice,
                    'id_trader' => $traderId,
                    'idProd' => $id_prod,
                    'quantiteC' => $quantiteC,
                ];

                $nSenders = Countdown::where('code_unique', $code_unique)
                    ->distinct()
                    ->pluck('nsender')
                    ->toArray();

                $decodednSenders = [];
                foreach ($nSenders as $nSender) {
                    $decodedValues = json_decode($nSender, true);
                    if (is_array($decodedValues)) {
                        $decodednSenders = array_merge($decodednSenders, $decodedValues);
                    }
                }


                $owner = User::find($nSender);
                if ($owner) {
                    // Notification::send($owner, new FacturegrouperNotifications($details));
                }


                // Mettre à jour le statut notified à true
                $countdown->update(['notified' => true]);
            }
        }
    }
}
