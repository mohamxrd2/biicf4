<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Projet;
use App\Models\Wallet;
use App\Models\gelement;
use App\Models\Countdown;
use App\Models\CommentTaux;
use App\Models\Transaction;
use App\Models\DemandeCredi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GagnantProjetNotifications;

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
            // Traitement spécifique en fonction de la différence
            if ($countdown->difference === 'credit_taux') {
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
                        'taux' => $lowestTauxComment->taux,
                    ]);

                    if ($countdown->difference === 'credit_taux') {
                        Log::info('Préparation des détails de la notification.', [
                            'countdown_id' => $countdown->id,
                        ]);

                        $taux = $lowestTauxComment->taux;
                        $id_invest = $lowestTauxComment->id_invest;
                        $id_emp = $lowestTauxComment->id_emp;
                        $ID = $lowestTauxComment->code_unique;

                        // Recherche du crédit associé
                        $credit = DemandeCredi::where('demande_id', $ID)->first();

                        if ($credit) {
                            Log::info('Crédit trouvé.', ['credit_id' => $credit->id]);
                        } else {
                            Log::error('Aucun crédit trouvé pour code_unique.', ['code_unique' => $ID]);
                            return; // Arrêter l'exécution si aucun crédit n'est trouvé
                        }

                        // Définir les détails de la notification
                        $details = [
                            'taux' => $taux,
                            'id_invest' => $id_invest,
                            'id_emp' => $id_emp,
                            'credit_id' => $credit->id,
                            'duree' => $credit->duree,
                            'montant' => $credit->montant,
                            'type_financement' => $credit->type_financement,
                        ];

                        // Récupérer l'utilisateur (investisseur)
                        Log::info('Début du traitement pour l\'investisseur.', ['id_invest' => $id_invest]);
                        $owner = User::find($id_invest);
                        if (!$owner) {
                            Log::warning('Utilisateur introuvable.', ['id_invest' => $id_invest]);
                        }

                        // Récupérer la liste des investisseurs
                        // Récupérer la liste des investisseurs
                        $investisseurs = $credit->id_investisseurs;

                        // Décoder les investisseurs si c'est une chaîne JSON
                        if (is_string($investisseurs)) {
                            Log::info('Décodage de la liste des investisseurs JSON.', ['investisseurs_brut' => $investisseurs]);
                            $investisseurs = json_decode($investisseurs, true);

                            if (json_last_error() !== JSON_ERROR_NONE) {
                                Log::error('Erreur lors du décodage JSON des investisseurs.', ['erreur' => json_last_error_msg()]);
                                $investisseurs = []; // Si une erreur survient, on initialise un tableau vide pour éviter les erreurs
                            }
                        }

                        // Vérifier si la liste est maintenant un tableau
                        if (!is_array($investisseurs)) {
                            Log::warning('La liste des investisseurs n\'est pas un tableau valide après tentative de décodage.');
                            $investisseurs = []; // Initialiser un tableau vide si nécessaire
                        } else {
                            Log::info('Liste initiale des investisseurs récupérée et décodée.', ['investisseurs' => $investisseurs]);
                        }

                        // Exclure l'investisseur courant ($id_invest)
                        Log::info('Exclusion de l\'investisseur courant.', ['id_invest' => $id_invest]);
                        $investisseurs = array_filter($investisseurs, function ($investisseur) use ($id_invest) {
                            return $investisseur != $id_invest; // Utiliser != pour éviter un problème de comparaison
                        });

                        // Réindexer les clés
                        $investisseurs = array_values($investisseurs);
                        Log::info('Liste des investisseurs après exclusion et réindexation.', ['investisseurs' => $investisseurs]);

                        // Récupérer les informations sur le montant
                        $gelement = gelement::where('reference_id', $ID)->first();
                        if ($gelement) {
                            $montant = $gelement->amount;
                            Log::info('Montant récupéré pour le gelement.', ['reference_id' => $ID, 'amount' => $montant]);
                        } else {
                            $montant = 0;
                            Log::warning('Aucun gelement trouvé pour la référence.', ['reference_id' => $ID]);
                        }

                        // Dernier log pour vérifier la fin du traitement
                        Log::info('Fin du traitement pour l\'investisseur.', [
                            'id_invest' => $id_invest,
                            'investisseurs_final' => $investisseurs,
                            'montant' => $montant
                        ]);

                        // Vérifier que l'utilisateur existe avant d'envoyer la notification
                        if ($owner) {
                            $referenceId = $this->generateIntegerReference();

                            // Envoi de la notification
                            Notification::send($owner, new GagnantProjetNotifications($details));
                            Log::info('Notification envoyée à l\'investisseur principal.', [
                                'user_id' => $id_invest,
                                'notification_details' => $details,
                            ]);

                            // Mise à jour des portefeuilles des autres investisseurs
                            foreach ($investisseurs as $investisseur) {
                                $userWallet = Wallet::where('user_id', $investisseur)->first();

                                if ($userWallet) {
                                    $userWallet->increment('balance', $montant);
                                    Log::info('Portefeuille mis à jour.', [
                                        'user_id' => $investisseur,
                                        'montant_ajouté' => $montant,
                                    ]);

                                    // Création de la transaction
                                    $this->createTransactionNew(
                                        $credit->id_user,
                                        $investisseur,
                                        'Réception',
                                        'COC',
                                        $montant,
                                        $referenceId,
                                        'Restitution d\'argent'
                                    );
                                    Log::info('Transaction créée.', [
                                        'from' => $credit->id_user,
                                        'to' => $investisseur,
                                        'montant' => $montant,
                                        'reference' => $referenceId,
                                    ]);
                                } else {
                                    Log::warning('Portefeuille non trouvé pour un investisseur.', ['user_id' => $investisseur]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
        $transaction->save();
    }
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
}
