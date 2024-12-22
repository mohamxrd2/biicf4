<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Models\AppelOffreGrouper;
use App\Models\Countdown;
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
    protected $signature = 'app:appeloffreGrouper';
    protected $description = 'Gère les notifications pour les appels d\'offres soumis';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction(); // Démarre une transaction

        try {

            $countdowns = Countdown::where('notified', false)
                ->where('start_time', '<=', now()->subMinutes(2))
                ->where('difference', 'quantiteGrouper')
                ->with(['sender', 'achat', 'appelOffre','appelOffreGrouper'])
                ->get();

            // Récupérer tous les codes uniques des Countdown non notifiés
            $codeUniques = $countdowns->pluck('code_unique')->unique();

            // Récupérer les AppelOffreGrouper correspondants
            $appelsOffreGroups = AppelOffreGrouper::whereIn('codeunique', $codeUniques)->get();

            // Log des données pour vérification
            Log::info('Countdowns expirés:', $countdowns->toArray());
            Log::info('Groupes d\'AppelOffre correspondants:', $appelsOffreGroups->toArray());

            // Traiter chaque AppelOffreGroup
            foreach ($appelsOffreGroups as $appelOffreGroup) {
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
