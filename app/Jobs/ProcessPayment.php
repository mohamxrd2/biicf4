<?php

namespace App\Jobs;

use App\Models\Cotisation;
use App\Models\EchecPaiement;
use App\Models\Tontines;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Gelement;
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
        $userWallet = Wallet::where('user_id', $this->user->id)->first();

        if (!$userWallet) {
            Log::error("❌ Wallet introuvable pour l'utilisateur: {$this->user->id}");
            return;
        }

        if ($this->tontine->statut === '1st') {
            $gelement = Gelement::where('id_wallet', $userWallet->id)->first();

            if (!$gelement) {
                Log::error("❌ Gelement introuvable pour le wallet ID: {$userWallet->id}");
                return;
            }

            if ($gelement->amount < $this->tontine->montant_cotisation) {
                Log::warning("❌ Solde insuffisant dans Gelement pour l'utilisateur: {$this->user->id}");
                return;
            }

            $gelement->amount -= $this->tontine->montant_cotisation;
            $gelement->status = 'ok'; // Correction ici
            $gelement->save();

            $this->tontine->statut = 'active'; // Correction ici
            $this->tontine->save();
        } elseif ($userWallet->balance >= $this->tontine->montant_cotisation && $this->tontine->statut === 'active') {

            $userWallet->balance -= $this->tontine->montant_cotisation;
            $userWallet->save();
        } else {
            $this->handlePaymentFailure();
            return;
        }

        $transactionService->createTransaction(
            $this->user->id,
            $this->user->id,
            'Débit',
            $this->tontine->montant_cotisation,
            $this->generateIntegerReference(),
            'Paiement de cotisation',
            'COC'
        );

        Cotisation::create([
            'user_id' => $this->user->id,
            'tontine_id' => $this->tontine->id,
            'montant' => $this->tontine->montant_cotisation,
            'statut' => 'payé',
        ]);

        Notification::send($this->user, new tontinesNotification([
            'code_unique' => $this->generateIntegerReference(),
            'title' => 'Paiement effectué avec succès',
            'description' => 'Cliquez pour voir les détails de votre paiement.',
        ]));

        Log::info("✅ Paiement réussi : {$this->user->name} a payé {$this->tontine->montant_cotisation} pour la tontine.");
    }

    private function handlePaymentFailure()
    {
        $cotisation = Cotisation::create([
            'user_id' => $this->user->id,
            'tontine_id' => $this->tontine->id,
            'montant' => $this->tontine->montant_cotisation,
            'statut' => 'échec',
        ]);

        EchecPaiement::create([
            'user_id' => $this->user->id,
            'cotisation_id' => $cotisation->id,
            'montant_du' => $this->tontine->montant_cotisation,
        ]);

        Notification::send($this->user, new tontinesNotification([
            'code_unique' => $this->generateIntegerReference(),
            'title' => 'Échec de paiement',
            'description' => 'Cliquez pour voir les détails de votre paiement.',
        ]));

        Log::warning("❌ Échec de paiement : {$this->user->name} n'a pas assez de solde pour la tontine .");
    }

    private function generateIntegerReference()
    {
        return now()->timestamp . rand(1000, 9999);
    }
}
