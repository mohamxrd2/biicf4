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
                        'taux' => $lowestTauxComment->taux
                    ]);

                    // Assurez-vous que cette condition est correcte et qu'elle n'est pas juste une expression inutile
                    if ($countdown->difference === 'credit_taux') {
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

                        // Récupérer la liste des investisseurs
                        $investisseurs = $credit->id_investisseurs;

                        // Assurez-vous que $investisseurs est un tableau
                        if (is_string($investisseurs)) {
                            // Exemple si c'est une chaîne JSON
                            $investisseurs = json_decode($investisseurs, true);
                        } elseif (is_string($investisseurs)) {
                            // Exemple si c'est une chaîne CSV
                            $investisseurs = explode(',', $investisseurs);
                        }

                        // Exclure l'investisseur courant ($id_invest)
                        $investisseurs = array_filter($investisseurs, function ($investisseur) use ($id_invest) {
                            return $investisseur != $id_invest; // Utilisez != au lieu de !== si $investisseurs contient des chaînes
                        });

                        // Réindexer les clés
                        $investisseurs = array_values($investisseurs);

                        $gelement = gelement::where('reference_id', $ID)->first();

                        $montant =  $gelement->amount;



                        // Vérifier que l'utilisateur existe avant d'envoyer la notification
                        if ($owner) {

                            $referenceId = $this->generateIntegerReference();

                            Notification::send($owner, new GagnantProjetNotifications($details));

                            // Log après l'envoi de la notification
                            Log::info('Notification envoyée.', ['notification_details' => $details]);

                            foreach($investisseurs as $investisseur){
                                $userWallet = Wallet::where('user_id', $investisseur)->first();

                                if ($userWallet) {

                               $userWallet->increment('balance', $montant);


                                // Créer la transaction
                                $this->createTransactionNew($credit->id_user, $investisseur, 'Réception', 'COC', $montant, $referenceId, 'Rechargement SOS');

                                }



                            }


                            // Mettre à jour l'état du countdown après la notification
                            $countdown->update(['notified' => true]);
                        } else {
                            Log::warning('Aucun utilisateur trouvé avec cet ID.', ['id_invest' => $id_invest]);
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
