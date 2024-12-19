<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Models\AppelOffreGrouper;
use App\Models\User;
use App\Models\UserQuantites;
use App\Notifications\AppelOffreGrouperNotification;
use App\Notifications\Confirmation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AppeloffreCountdown extends Command
{
    protected $signature = 'app:appeloffre';
    protected $description = 'Gère les notifications pour les appels d\'offres soumis';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction(); // Démarre une transaction

        try {
            $appelOffreGroups = AppelOffreGrouper::where('count', true)
                ->where('started_at', '<=', now()->subMinutes(2))
                ->where(function ($query) {
                    $query->whereNull('notified') // Inclut les notified null
                        ->orWhere('notified', false); // Inclut les notified false
                })
                ->get();

            // Transformer la collection en JSON pour le log


            foreach ($appelOffreGroups as $appelOffreGroup) {

                $this->processAppelOffreGroup($appelOffreGroup);
            }
            DB::commit(); // Si tout se passe bien, commit les modifications
        } catch (\Exception $e) {
            DB::rollBack(); // Si une erreur se produit, annule les modifications

            // Enregistrer l'erreur dans les logs
            Log::error('Erreur lors du traitement des countdowns.', ['error' => $e->getMessage()]);
        }
    }

    private function processAppelOffreGroup($appelOffreGroup)
    {
        $codeUnique = $appelOffreGroup->codeunique;

        $this->notifyUsersQuantites($appelOffreGroup, $codeUnique);
        $this->notifyProdUsers($appelOffreGroup, $codeUnique);

        $this->markAppelOffreAsNotified($appelOffreGroup);
    }

    private function notifyUsersQuantites($appelOffreGroup, $codeUnique)
    {
        $userQuantites = userquantites::where('code_unique', $codeUnique)->get();

        foreach ($userQuantites as $userQuantite) {
            $user = User::find($userQuantite->user_id);

            if ($user) {
                $achatUser = [
                    'id' => $appelOffreGroup->id,
                    'idProd' => $appelOffreGroup->id_prod,
                    'code_unique' => $codeUnique,
                    'title' => 'Confirmation de commande',
                    'description' => 'Votre commande a été envoyée avec succès.',
                ];

                Notification::send($user, new Confirmation($achatUser));
                event(new NotificationSent($user));
            }
        }
    }

    private function notifyProdUsers($appelOffreGroup, $codeUnique)
    {
        $prodUsers = $appelOffreGroup->prodUsers;

        if (!$prodUsers) {
            Log::warning('Aucun prodUser trouvé pour cet appel d\'offre', [
                'code_unique' => $codeUnique,
            ]);
            return;
        }

        $decodedProdUsers = json_decode($prodUsers, true) ?? [];
        $totalPersonnes = count($decodedProdUsers);

        foreach ($decodedProdUsers as $prodUserId) {
            $owner = User::find($prodUserId);

            if ($owner) {
                $data = [
                    'id_appelGrouper' => $appelOffreGroup->id,
                    'totalPersonnes' => $totalPersonnes,
                    'code_unique' => $codeUnique,
                    'title' => 'Négociation d\'une commande groupée',
                    'description' => 'Cliquez pour participer à la négociation.',
                ];

                Notification::send($owner, new AppelOffreGrouperNotification($data));
                event(new NotificationSent($owner));
            }
        }
    }

    private function markAppelOffreAsNotified($appelOffreGroup)
    {
        $appelOffreGroup->update(['notified' => true]);
    }
}
