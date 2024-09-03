<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\groupagefact;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\GrouperFactureNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class GrouperFacture extends Command
{
    protected $signature = 'app:grouper-facture';
    protected $description = 'Command to send notifications for grouped invoices';

    public function handle()
    {
        // $groupagefacts = groupagefact::where('notified', false)
        //     ->where('start_time', '<=', now()->subMinutes(2))
        //     ->get();

        // foreach ($groupagefacts as $groupagefact) {
        //     $code_unique = $groupagefact->code_unique;

        //     $lowestPriceComment = Comment::with('user')
        //         ->where('code_unique', $code_unique)
        //         ->orderBy('prixTrade', 'asc')
        //         ->first();

        //     if ($lowestPriceComment) {
        //         $lowestPrice = $lowestPriceComment->prixTrade;
        //         $traderId = $lowestPriceComment->id_trader;
        //         $id_prod = $lowestPriceComment->id_prod;
        //         $quantiteC = $lowestPriceComment->quantiteC;

        //         $usersenders = groupagefact::where('code_unique', $code_unique)
        //             ->distinct()
        //             ->pluck('usersenders')
        //             ->toArray();

        //         $decodedProdUsers = [];
        //         foreach ($usersenders as $usersender) {
        //             $decodedValues = json_decode($usersender, true);
        //             if (is_array($decodedValues)) {
        //                 $decodedProdUsers = array_merge($decodedProdUsers, $decodedValues);
        //             }
        //         }

        //         foreach ($decodedProdUsers as $prodUser) {
        //             // Calculer la quantité totale pour chaque utilisateur
        //             $userQuantity = userquantites::where('code_unique', $code_unique)
        //                 ->where('user_id', $prodUser)
        //                 ->sum('quantite'); // La somme est un nombre, pas un objet

        //             $data = [
        //                 'code_unique' => $code_unique,
        //                 'prixTrade' => $lowestPrice,
        //                 'id_trader' => $traderId,
        //                 'idProd' => $id_prod,
        //                 'quantiteC' => $userQuantity, // Utilisez la somme calculée
        //             ];

        //             $requiredKeys = ['code_unique', 'prixTrade', 'id_trader', 'idProd', 'quantiteC'];
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
        //         // Supprimer les commentaires avec ce code unique
        //         Comment::where('code_unique', $code_unique)->delete();
        //         // Supprimer les commentaires avec ce code unique
        //         userquantites::where('code_unique', $code_unique)->delete();


        //         // Mettre à jour la facture pour indiquer qu'une notification a été envoyée
        //         $groupagefact->update(['notified' => true]);
        //     }
        // }
    }
}
