<?php

namespace App\Console\Commands;

use App\Models\AjoutMontant;
use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\Projet;
use App\Models\User;
use App\Notifications\GagnantProjetNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class projetGrouper extends Command
{
    protected $signature = 'app:projet-groupe';
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
            // Récupérer le code unique du countdown
            $code_unique = $countdown->code_unique;

            // Log pour le traitement d'un countdown spécifique
            Log::info('Traitement du countdown', ['countdown_id' => $countdown->id, 'code_unique' => $code_unique]);

            // Vérifier si le countdown correspond à une notification pour ajout de montant
            if ($countdown->difference === 'projet_compo') {
                // Récupérer les enregistrements d'ajout de montant associés au projet
                $ajoutMontants = AjoutMontant::where('id_projet', $code_unique)->get();

                // Log pour vérifier le nombre de montants récupérés
                Log::info('Nombre de montants récupérés pour le projet', ['countdown_id' => $countdown->id, 'montant_count' => $ajoutMontants->count()]);

                foreach ($ajoutMontants as $ajoutMontant) {
                    // Extraire les détails du montant et de l'investisseur
                    $id_invest = $ajoutMontant->id_invest;
                    $montant = $ajoutMontant->montant;
                    $id_projet = $ajoutMontant->id_projet;
                    $projet = Projet::find($id_projet); // Recherche du projet correspondant

                    // Définir les détails de la notification
                    $details = [
                        'montant' => $montant,
                        'id_invest' => $id_invest,
                        'projet_id' => $id_projet,
                        'duree' => $projet->durer ?? null,
                        'type_financement' => 'grouper',
                    ];

                    // Log avant d'envoyer la notification
                    Log::info('Envoi de la notification pour l\'ajout de montant.', ['id_invest' => $id_invest, 'montant' => $montant]);

                    // Récupérer l'utilisateur (investisseur)
                    $owner = User::find($id_invest);

                    // Vérifier que l'utilisateur existe avant d'envoyer la notification
                    if ($owner) {
                        Notification::send($owner, new GagnantProjetNotifications($details));

                        // Log après l'envoi de la notification
                        Log::info('Notification envoyée.', ['notification_details' => $details]);
                    } else {
                        Log::warning('Aucun utilisateur trouvé avec cet ID.', ['id_invest' => $id_invest]);
                    }
                }

                // Mettre à jour l'état du countdown après avoir traité tous les ajouts de montant
                $countdown->update(['notified' => true]);
            } else {
                // Log si le countdown n'est pas pour ajout de montant
                Log::warning('Le countdown ne correspond pas à un ajout de montant.', ['countdown_id' => $countdown->id]);
            }
        }
    }
}
