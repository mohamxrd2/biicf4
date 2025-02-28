<?php

namespace App\Jobs;

use App\Models\Cedd;
use App\Models\Cefp;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Gelement;
use App\Models\Tontines;
use App\Models\Cotisation;
use App\Models\Transaction;
use App\Models\EchecPaiement;
use Illuminate\Bus\Queueable;
use App\Models\ComissionAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TransactionService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\tontinesNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
            Log::error("Wallet introuvable pour l'utilisateur", ['user_id' => $this->user->id]);
            return;
        }

        try {
            DB::beginTransaction();

            if ($this->tontine->statut === '1st') {
                // Récupérer le gelement spécifique à cette tontine
                $gelement = gelement::where('reference_id', $this->tontine->gelement_reference)
                    ->where('id_wallet', $userWallet->id)
                    ->where('status', 'pending')
                    ->first();

                if (!$gelement || $gelement->amount < $this->tontine->montant_cotisation) {
                    throw new \Exception("Gelement insuffisant ou invalide");
                }

                // Déduire les frais de gestion du gelement
                $gelement->decrement('amount', $this->tontine->frais_gestion);

                // Calculer le reste et l'envoyer au solde disponible (CEDD) si existant
                $reste = $gelement->amount;
                if ($reste > 0) {
                    if ($this->tontine->isUnlimited) {
                        $userWallet->cefp->increment('Solde', $reste);
                    } else {
                        $userWallet->cedd->increment('Solde', $reste);
                    }
                    
                    $gelement->update(['amount' => 0]);
                }

                // Mettre à jour le statut du gelement
                $gelement->update(['status' => 'OK']);

                // frais de service pour l'admin
                $adminWallet = ComissionAdmin::where('admin_id', 1)->first();
                if ($adminWallet) {
                    $adminWallet->increment('balance', $this->tontine->frais_gestion);
                    $this->createTransactionAdmin(
                        $this->user->id,
                        1,
                        'Commission',
                        $this->tontine->frais_gestion,
                        $this->generateIntegerReference(),
                        'Commission de BICF',
                        'effectué',
                        'commission'
                    );
                }

                // Créer la transaction pour l'utlisateur
                $this->createTransactionAdmin(
                    $this->user->id,
                    1,
                    'Envoie',
                    $this->tontine->montant_cotisation,
                    $this->generateIntegerReference(),
                    'Frais de service',
                    'effectué',
                    'COC'
                );


                $this->tontine->update(['statut' => 'active']);

                Notification::send($this->user, new tontinesNotification([
                    'title' => 'Paiement effectué avec succès',
                    'description' => 'Cliquez pour voir les détails.'
                ]));

                // Créer la transaction et la cotisation
                $reference = $this->generateIntegerReference();

                $transactionService->createTransaction(
                    $this->user->id,
                    $this->user->id,
                    'Débit',
                    $this->tontine->montant_cotisation,
                    $reference,
                    'Paiement de cotisation',
                    'COC'
                );
            } else {
                // Pour les paiements réguliers, vérifier le solde disponible
                // en tenant compte des autres tontines actives
                $montantTotalEngagé = $this->calculateMontantEngagé($this->user->id);
                $soldeDisponible = $userWallet->balance - $montantTotalEngagé;


                if ($soldeDisponible < $this->tontine->montant_cotisation) {
                    throw new \Exception("Solde insuffisant après engagements");
                }

                $userWallet->balance -= $this->tontine->montant_cotisation;

                if ($this->tontine->isUnlimited) {
                    $userCedd = Cefp::where('id_wallet', $userWallet->id)->first();
                    $userCedd->increment('Solde', $this->tontine->montant_cotisation);
                    $userWallet->save();
                } else {
                    $userCedd = Cedd::where('id_wallet', $userWallet->id)->first();
                    $userCedd->increment('Solde', $this->tontine->montant_cotisation);
                    $userWallet->save();
                }
            }


            Cotisation::create([
                'user_id' => $this->user->id,
                'tontine_id' => $this->tontine->id,
                'montant' => $this->tontine->montant_cotisation,
                'statut' => 'payé'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->handlePaymentFailure($this->user, $this->tontine);
        }
    }
    private function calculateMontantEngagé($userId)
    {
        // Calculer le montant total engagé dans toutes les tontines actives
        $tontinesActives = Tontines::where('user_id', $userId)
            ->where('statut', 'active')
            ->where('date_fin', '>=', now())
            ->get();

        $montantEngagé = 0;
        foreach ($tontinesActives as $tontine) {
            $montantEngagé += $tontine->montant_cotisation;
        }

        return $montantEngagé;
    }
    protected function createTransactionAdmin(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_admin_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = $status;
        $transaction->type_compte = $type_compte;

        $transaction->save();
    }


    private function handlePaymentFailure(User $user, Tontines $tontine)
    {
        $cotisation = Cotisation::create([
            'user_id' => $user->id,
            'tontine_id' => $tontine->id,
            'montant' => $tontine->montant_cotisation,
            'statut' => 'échec'
        ]);

        EchecPaiement::create([
            'user_id' => $user->id,
            'cotisation_id' => $cotisation->id,
            'montant_du' => $tontine->montant_cotisation
        ]);

        Notification::send($user, new tontinesNotification([
            'title' => 'Échec de paiement',
            'description' => 'Cliquez pour voir les détails.'
        ]));
    }

    private function generateIntegerReference()
    {
        return now()->timestamp . rand(1000, 9999);
    }
}
