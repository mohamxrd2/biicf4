<?php

namespace App\Jobs;

use App\Models\EchecPaiement;
use App\Models\Tontine;
use App\Models\Tontines;
use App\Models\User;
use App\Models\PaymentFailure;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

        if ($this->user->balance >= $this->tontine->montant_cotisation) {
            // Déduction du solde de l'utilisateur
            $this->user->balance -= $this->tontine->montant_cotisation;
            $this->user->save();

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

            Log::info("✅ Paiement réussi : {$this->user->name} a payé {$this->tontine->montant_cotisation} pour la tontine {$this->tontine->nom}.");
        } else {
            // Stocker l’échec de paiement
            EchecPaiement::create([
                'user_id' => $this->user->id,
                'tontine_id' => $this->tontine->id,
                'amount' => $this->tontine->montant_cotisation,
                'failure_reason' => 'Solde insuffisant',
                'attempted_at' => now(),
            ]);

            Log::warning("❌ Échec de paiement : {$this->user->name} n'a pas assez de solde pour la tontine {$this->tontine->nom}.");
        }
    }

    private function generateIntegerReference()
    {
        return now()->timestamp . rand(1000, 9999);
    }
}
