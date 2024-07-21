<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Models\User;
use App\Notifications\GrouperFactureNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class GrouperFacture extends Command
{

    protected $signature = 'app:grouper-facture';

    protected $description = 'Command description';

    public function handle()
    {
        // $appelOffreGroups = AppelOffreGrouper::whereNotNull('notified')
        //     ->where('created_at', '<=', now()->subMinutes(1))
        //     ->get();

        // foreach ($appelOffreGroups as $appelOffreGroup) {
            // Récupérer le code unique
    //         $code_unique = $countdown->code_unique;

    //         // Retrouver l'enregistrement avec le prix le plus bas parmi les enregistrements avec ce code unique
    //         $lowestPriceComment = Comment::with('user')
    //             ->where('code_unique', $code_unique)
    //             ->orderBy('prixTrade', 'asc')
    //             ->first();


    //         // Vérifier si un enregistrement a été trouvé
    //         if ($lowestPriceComment) {
    //             $lowestPrice = $lowestPriceComment->prixTrade;
    //             $traderId = $lowestPriceComment->id_trader;
    //             $id_prod = $lowestPriceComment->id_prod;
    //             $quantiteC = $lowestPriceComment->quantiteC;
    //             $localite = $lowestPriceComment->localite;
    //             $specificite = $lowestPriceComment->specificite;
    //             $nameprod = $lowestPriceComment->nameprod;
    //             $id_sender = $lowestPriceComment->id_sender;

    //         $usergroup = AppelOffreGrouper::where('codeunique', $codesUniques)
    //             ->distinct()
    //             ->pluck('user_id')
    //             ->toArray();

    //         $prodUsers = AppelOffreGrouper::where('codeunique', $codesUniques)
    //             ->distinct()
    //             ->pluck('prodUsers')
    //             ->toArray();

    //         $decodedProdUsers = [];
    //         foreach ($prodUsers as $prodUser) {
    //             $decodedValues = json_decode($prodUser, true);
    //             if (is_array($decodedValues)) {
    //                 $decodedProdUsers = array_merge($decodedProdUsers, $decodedValues);
    //             }
    //         }



    //         foreach ($decodedProdUsers as $prodUser) {
    //             $data = [
    //                 'sender_name' => $countdown->sender->name, // Ajouter le nom de l'expéditeur aux détails de la notification
    //                 'code_unique' => $countdown->code_unique,
    //                 'prixTrade' => $lowestPrice,
    //                 'id_trader' => $traderId,
    //                 'idProd' => $id_prod,
    //                 'quantiteC' => $quantiteC,
    //             ];

    //             $requiredKeys = ['dateTot', 'dateTard', 'productName', 'quantity', 'payment', 'Livraison', 'specificity', 'image', 'prodUsers', 'code_unique'];
    //             foreach ($requiredKeys as $key) {
    //                 if (!array_key_exists($key, $data)) {
    //                     throw new \InvalidArgumentException("La clé '$key' est manquante dans \$data.");
    //                 }
    //             }

    //             $owner = User::find($prodUser);

    //             if ($owner) {
    //                 Notification::send($owner, new GrouperFactureNotifications($data));
    //             }
    //         }

    //     }

    //     return 0;
    }
}
