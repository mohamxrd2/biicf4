<?php

namespace App\Console\Commands;

use App\Models\AppelOffreGrouper;
use App\Models\User;
use App\Notifications\AppelOffreGrouperNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
            ->where('created_at', '<=', now()->subMinutes(2))
            ->get();

        foreach ($appelOffreGroups as $appelOffreGroup) {
            $codesUniques = $appelOffreGroup->codeunique;
            $dateTot = $appelOffreGroup->dateTot;
            $dateTard = $appelOffreGroup->dateTard;

            Log::info('Traitement d\'un groupe d\'appel d\'offre', [
                'codeunique' => $codesUniques,
                'dateTot' => $dateTot,
                'dateTard' => $dateTard,
            ]);

            $prodUsers = AppelOffreGrouper::where('codeunique', $codesUniques)
                ->distinct()
                ->pluck('prodUsers')
                ->toArray();

            Log::info('Liste des prodUsers extraits', [
                'prodUsers' => $prodUsers,
            ]);

            // Exemple : validation des données
            if (empty($prodUsers)) {
                Log::warning('Aucun prodUser trouvé pour ce codeunique', [
                    'codeunique' => $codesUniques,
                ]);
            }

            $decodedProdUsers = [];
            foreach ($prodUsers as $prodUser) {
                $decodedValues = json_decode($prodUser, true);
                if (is_array($decodedValues)) {
                    $decodedProdUsers = array_merge($decodedProdUsers, $decodedValues);
                }
            }

            $sumquantite = AppelOffreGrouper::where('codeunique', $codesUniques)->sum('quantity');
            $totalPersonnes = count($decodedProdUsers); // Nombre total de personnes

            foreach ($decodedProdUsers as $prodUser) {
                $data = [
                    'dateTot' => $dateTot,
                    'dateTard' => $dateTard,
                    'productName' => $appelOffreGroup->productName, // Assurez-vous que $productName est bien défini
                    'totalPersonnes' => $totalPersonnes, // Nombre total de personnes
                    'quantiteTotale' => $sumquantite, // Quantité totale
                    'code_unique' => $codesUniques // Quantité totale
                ];

                $owner = User::find($prodUser);

                if ($owner) {
                    Notification::send($owner, new AppelOffreGrouperNotification($data));
                }
            }

            // AppelOffreGrouper::where('codeunique', $codesUniques)->delete();
        }
    }
}
