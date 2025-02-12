<?php

namespace App\Jobs;

use App\Models\Cotisation;
use App\Models\EchecPaiement;
use App\Models\Tontines;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\tontinesNotification;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public Tontines $tontine;

    public function __construct(User $user, Tontines $tontine)
    {
        $this->user = $user;
        $this->tontine = $tontine;
    }

    public function handle()
    {
        $transactionService = new TransactionService();

        // Récupérer le portefeuille utilisateur
        $userWallet = Wallet::where('user_id', $this->user->id)->first();

        if ($userWallet && $userWallet->balance >= $this->tontine->montant_cotisation) {
            // Déduction du solde de l'utilisateur
            $userWallet->balance -= $this->tontine->montant_cotisation;
            $userWallet->save();

            // Création de la transaction
            $transactionService->createTransaction(
                $this->user->id,
                $this->tontine->id,
                'Débit',
                $this->tontine->montant_cotisation,
                $this->generateIntegerReference(),
                'Paiement de cotisation',
                'TONTINE'
            );

            // Enregistrement de la cotisation
            Cotisation::create([
                'user_id' => $this->user->id,
                'tontine_id' => $this->tontine->id,
                'montant' => $this->tontine->montant_cotisation,
                'statut' => 'payé',
            ]);

            // Envoi de notification
            Notification::send($this->user, new tontinesNotification([
                'code_unique' => $this->generateIntegerReference(),
                'title' => 'Paiement effectué avec succès',
                'description' => 'Cliquez pour voir les détails de votre paiement.',
            ]));

            Log::info("✅ Paiement réussi : {$this->user->name} a payé {$this->tontine->montant_cotisation} pour la tontine {$this->tontine->nom}.");
        } else {
            // Enregistrement de l'échec de cotisation
            $cotisation = Cotisation::create([
                'user_id' => $this->user->id,
                'tontine_id' => $this->tontine->id,
                'montant' => $this->tontine->montant_cotisation,
                'statut' => 'échec',
            ]);

            // Stocker l’échec de paiement
            EchecPaiement::create([
                'user_id' => $this->user->id,
                'cotisation_id' => $cotisation->id,
                'montant_du' => $this->tontine->montant_cotisation,
            ]);

            // Envoi de notification d'échec
            Notification::send($this->user, new tontinesNotification([
                'code_unique' => $this->generateIntegerReference(),
                'title' => 'Échec de paiement',
                'description' => 'Cliquez pour voir les détails de votre paiement.',
            ]));

            Log::warning("❌ Échec de paiement : {$this->user->name} n'a pas assez de solde pour la tontine {$this->tontine->nom}.");
        }
    }

    private function generateIntegerReference()
    {
        return now()->timestamp . rand(1000, 9999);
    }
}
