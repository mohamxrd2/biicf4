<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\groupagefact;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\GrouperFactureNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class projetCountdown extends Command
{
    protected $signature = 'app:projet-countdown';
    protected $description = 'Command to send notifications for grouped invoices';

    public function handle()
    {
        $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(2))
            ->with('sender') // Charger la relation userSender
            ->get();


        foreach ($countdowns as $countdown) {
            // Récupérer le code unique
            $code_unique = $countdown->code_unique;
            // Log::info('Traitement du countdown.', ['countdown_id' => $countdown->id, 'code_unique' => $code_unique]);

            // Retrouver l'enregistrement avec le prix le plus bas parmi les enregistrements avec ce code unique
            $lowestTauxComment = CommentTaux::with('investisseur')
                ->where('code_unique', $code_unique)
                ->orderBy('taux', 'asc')
                ->first();
            // Log::info('Commentaire avec le prix le plus bas récupéré.', ['lowestPriceComment_id' => $lowestPriceComment->id ?? null]);
            if ($lowestTauxComment) {
                $countdown->difference === 'taux_projet';
                if ($countdown) {
                    // Extraire les détails pour la notification
                    Log::info('Préparation des détails de la notification.', ['countdown' => $countdown->id]);

                    $taux = $countdown->prixTrade;
                    $code_unique = $countdown->code_unique;
                    $id_invest = $countdown->id_invest;
                    $id_emp = $countdown->id_emp;
                    $id_projet = $countdown->id_projet;

                    // Définir les détails de la notification
                    $details = [
                        'taux' => $countdown->sender->id ?? null, // Ajouter le nom de l'expéditeur aux détails de la notification
                        'code_unique' => $countdown->code_unique,
                        'id_invest' => $id_invest,
                        'id_emp' => $id_emp,
                        'id_projet' => $id_projet,

                    ];
                    // Vérifier le type de notification à envoyer
                    if ($countdown->difference === 'taux_projet') {
                        // $notification = $countdown->user->notifications()->where('type', AppelOffreTerminer::class)->latest()->first();
                        // if ($notification) {
                        //     Log::info('Mise à jour de la notification existante.', ['notification_id' => $notification->id]);
                        //     $notification->update(['type_achat' => 'taux']);
                        // }
                    }
                }
            }
        }
    }
}
