<?php

namespace App\Console\Commands;

use App\Models\AppelOffreGrouper;
use App\Models\User;
use App\Notifications\AppelOffreGrouperNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class AppeloffreCountdown extends Command
{
    protected $signature = 'app:appeloffre';

    protected $description = 'When appeloffre submit and send the notification';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $appelOffreGroups = AppelOffreGrouper::whereNotNull('productName')
            ->where('created_at', '<=', now()->subMinutes(1))
            ->get();

        foreach ($appelOffreGroups as $appelOffreGroup) {
            $codesUniques = $appelOffreGroup->codeunique;
            $dateTot = $appelOffreGroup->dateTot;
            $dateTard = $appelOffreGroup->dateTard;
            $productName = $appelOffreGroup->productName;
            $quantity = $appelOffreGroup->quantity;
            $payment = $appelOffreGroup->payment;
            $livraison = $appelOffreGroup->livraison;
            $specificity = $appelOffreGroup->specificity;
            $localite = $appelOffreGroup->localite;
            $lowestPricedProduct = $appelOffreGroup->lowestPricedProduct;

            $usergroup = AppelOffreGrouper::where('codeunique', $codesUniques)
                ->distinct()
                ->pluck('user_id')
                ->toArray();

            $prodUsers = AppelOffreGrouper::where('codeunique', $codesUniques)
                ->distinct()
                ->pluck('prodUsers')
                ->toArray();

            $decodedProdUsers = [];
            foreach ($prodUsers as $prodUser) {
                $decodedValues = json_decode($prodUser, true);
                if (is_array($decodedValues)) {
                    $decodedProdUsers = array_merge($decodedProdUsers, $decodedValues);
                }
            }

            $sumquantite = AppelOffreGrouper::where('codeunique', $codesUniques)->sum('quantity');

            $appelOffreGroupcount = AppelOffreGrouper::where('codeunique', $codesUniques)
                ->distinct('user_id')
                ->count('user_id');

            foreach ($decodedProdUsers as $prodUser) {
                $data = [
                    'dateTot' => $dateTot,
                    'dateTard' => $dateTard,
                    'productName' => $productName,
                    'quantity' => $sumquantite,
                    'payment' => $payment,
                    'Livraison' => $livraison,
                    'localite' => $localite,
                    'specificity' => $specificity,
                    'difference' => 'grouper',
                    'image' => null,
                    'id_sender' => $usergroup,
                    'prodUsers' => $prodUser,
                    'lowestPricedProduct' => $lowestPricedProduct,
                    'code_unique' => $codesUniques,
                ];

                $requiredKeys = ['dateTot', 'dateTard', 'productName', 'quantity', 'payment', 'Livraison', 'specificity', 'image', 'prodUsers', 'code_unique'];
                foreach ($requiredKeys as $key) {
                    if (!array_key_exists($key, $data)) {
                        throw new \InvalidArgumentException("La clé '$key' est manquante dans \$data.");
                    }
                }

                $owner = User::find($prodUser);

                if ($owner) {
                    Notification::send($owner, new AppelOffreGrouperNotification($data));
                }
            }

            AppelOffreGrouper::where('codeunique', $codesUniques)->delete();
        }

        return 0;
    }
}
