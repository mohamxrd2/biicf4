<?php

namespace App\Jobs;

use App\Models\Crp;
use App\Models\Coi;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;
use App\Services\CommissionService;
use App\Notifications\remboursement;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class TraiterRemboursementCredit implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $credit;
    protected $wallet;

    public function __construct($credit, $wallet)
    {
        $this->credit = $credit;
        $this->wallet = $wallet;
    }

    public function handle()
    {
        $credit = $this->credit;
        $wallet = $this->wallet;

        $decodedInvestisseurs = json_decode($credit->investisseurs, true);
        $investisseursMontants = [];
        $investisseursMontantsSansInteret = [];

        foreach ($decodedInvestisseurs as $investisseur) {
            if (isset($investisseur['investisseur_id'], $investisseur['montant_finance'])) {
                $id = $investisseur['investisseur_id'];
                $montant = $investisseur['montant_finance'];
                $interet = ($montant * $credit->taux_interet) / 100;

                $investisseursMontants[$id] = $montant + $interet;
                $investisseursMontantsSansInteret[$id] = $montant;
            }
        }

        Log::info('Liste des IDs des investisseurs : ' . implode(', ', array_keys($investisseursMontants)));

        DB::beginTransaction();
        try {
            $crp = Crp::where('id_wallet', $wallet->id)->first();
            if ($crp && $crp->Solde >= $credit->montant) {
                $crp->Solde -= $credit->montant;
                $crp->save();
            } else {
                Log::warning('Solde CRP insuffisant ou CRP non trouvé.', ['wallet_id' => $wallet->id]);
                return;
            }

            foreach ($investisseursMontantsSansInteret as $id => $montant) {
                $roi = $montant * $credit->taux_interet / 100;
                $commission = $roi * 0.1;
                $montantTotal = ($montant + $roi) - $commission;

                $walletInvest = Wallet::where('user_id', $id)->first();
                $coi = Coi::where('id_wallet', $walletInvest->id)->first();
                if ($coi) {
                    $coi->Solde += $montantTotal;
                    $coi->save();

                    Log::info('Mise à jour COI', [
                        'investisseur_id' => $id,
                        'montant' => $montantTotal
                    ]);
                }

                $transactionService = new TransactionService();
                $transactionService->createTransaction($credit->emprunteur_id, $id, 'Envoie', $montantTotal, $this->generateIntegerReference(), 'Remboursement de financement', 'effectué', $crp->type_compte);
                $transactionService->createTransaction($credit->emprunteur_id, $id, 'Réception', $montantTotal, $this->generateIntegerReference(), 'Remboursement de financement', 'effectué', $coi->type_compte);

                $investisseur = User::find($id);
                Notification::send($investisseur, new remboursement('Paiement de crédit effectué avec succès.'));

                $commissionService = new CommissionService();
                $commissionService->handleCommissions($commission, $investisseur->parrain);
            }

            $credit->statut = 'payé';
            $credit->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Erreur remboursement crédit : ' . $e->getMessage());
            throw $e;
        }
    }

    protected function generateIntegerReference(): int
    {
        return (int) (now()->getTimestamp() * 1000 + now()->micro);
    }
}
