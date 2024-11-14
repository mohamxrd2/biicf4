<?php

namespace App\Console\Commands;

use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\DemandeCredi;
use App\Models\Projet;
use App\Models\User;
use App\Notifications\GagnantProjetNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class creditCountdown extends Command
{
    protected $signature = 'app:credit-countdown';
    protected $description = 'Command to send notifications for grouped invoices';

    public function handle()
    {
        $countdowns = Countdown::where('notified', false)
            // ->where('start_time', '<=', now())
            ->where('start_time', '<=', now()->subMinutes(1))
            ->get();

        // Log pour vérifier le nombre de countdowns récupérés
        Log::info('Nombre de countdowns récupérés : ', ['countdown_count' => $countdowns->count()]);

        foreach ($countdowns as $countdown) {
            // Récupérer le code unique
            $code_unique = $countdown->code_unique;

            // Log pour le traitement d'un countdown spécifique
            Log::info('Traitement du countdown', ['countdown_id' => $countdown->id, 'code_unique' => $code_unique]);

            // Retrouver l'enregistrement avec le taux le plus bas, et en cas d'égalité, prendre le plus ancien
            $lowestTauxComment = CommentTaux::with('investisseur')
                ->where('code_unique', $code_unique) // Filtrer par le code unique du credit
                ->orderBy('taux', 'asc')           // Trier par le taux le plus bas en premier
                ->orderBy('created_at', 'asc')     // En cas d'égalité, trier par la date de création (le plus ancien en premier)
                ->first();                         // Récupérer le premier résultat

            // Vérifier si un commentaire avec le taux le plus bas a été trouvé
            if ($lowestTauxComment) {
                Log::info('Commentaire avec le taux le plus bas récupéré.', [
                    'comment_taux_id' => $lowestTauxComment->id,
                    'taux' => $lowestTauxComment->taux
                ]);

                // Assurez-vous que cette condition est correcte et qu'elle n'est pas juste une expression inutile
                if ($countdown->difference === 'projet_taux') {
                    // Log pour signaler que le countdown est prêt pour une notification
                    Log::info('Préparation des détails de la notification.', ['countdown' => $countdown->id]);

                    $taux = $lowestTauxComment->taux;
                    $id_invest = $lowestTauxComment->id_invest;
                    $id_emp = $lowestTauxComment->id_emp;
                    $ID = $lowestTauxComment->code_unique; // ID du credit récupéré

                    // Recherche d'un credit dans la table 'DemandeCredi' où 'demande_id' correspond au 'code_unique'
                    $credit = DemandeCredi::where('demande_id', $ID)->first(); // Récupérer le premier credit qui correspond

                    // Vérifier si un credit a été trouvé
                    if ($credit) {
                        // Vous pouvez maintenant accéder aux données de $credit
                        Log::info('Projet trouvé : ' . $credit->id);
                    } else {
                        // Si aucun credit n'est trouvé
                        Log::error('Aucun credit trouvé pour code_unique : ' . $ID);
                    }

                    // Définir les détails de la notification
                    $details = [
                        'taux' => $taux ?? null,
                        'id_invest' => $id_invest,
                        'id_emp' => $id_emp,
                        'credit_id' => $credit->id,
                        'duree' => $credit->duree,
                        'montant' => $credit->montant,
                        'type_financement' => $credit->type_financement,
                    ];

                    // Log avant d'envoyer la notification
                    Log::info('Envoi de la notification pour le credit.', ['id_invest' => $id_invest]);

                    // Récupérer l'utilisateur (investisseur)
                    $owner = User::find($id_invest);

                    // Vérifier que l'utilisateur existe avant d'envoyer la notification
                    if ($owner) {
                        Notification::send($owner, new GagnantProjetNotifications($details));

                        // Log après l'envoi de la notification
                        Log::info('Notification envoyée.', ['notification_details' => $details]);

                        // Mettre à jour l'état du countdown après la notification
                        $countdown->update(['notified' => true]);
                    } else {
                        Log::warning('Aucun utilisateur trouvé avec cet ID.', ['id_invest' => $id_invest]);
                    }
                }
            } else {
                // Log si aucun commentaire avec le taux le plus bas n'a été trouvé
                Log::warning('Aucun commentaire avec le taux le plus bas trouvé pour ce countdown.', ['countdown_id' => $countdown->id]);
            }
        }
    }
}
