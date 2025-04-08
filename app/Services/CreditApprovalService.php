<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Cfa;
use App\Models\credits_groupé;
use App\Models\Remboursements;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditApprovalService
{
    public function approuver(
        $montant,
        $userId,
        $demandeCredit,
        $coi,
        $notification,
        callable $generateReferenceCode,
        callable $createTransaction
    ) {
        $montant = floatval($montant);

        if ($montant <= 0) {
            return ['error' => 'Montant invalide.'];
        }

        $walletDemandeur = Wallet::where('user_id', $userId)->first();

        if (!$walletDemandeur) {
            return ['error' => 'Votre portefeuille est introuvable.'];
        }

        if ($coi->Solde < $montant) {
            return ['error' => 'Votre solde est insuffisant pour cette transaction.'];
        }

        DB::beginTransaction();

        try {
            if (!$coi) {
                return ['error' => 'Compte COI introuvable.'];
            }

            $coi->Solde -= $montant;
            $coi->save();

            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();

            if ($cfa) {
                $cfa->Solde += $montant;
                $cfa->save();
            }

            $debut = Carbon::parse($demandeCredit->date_fin);
            $durer = Carbon::parse($demandeCredit->duree);
            $jours = $debut->diffInDays($durer);

            $montantComission = $montant * 0.01;
            $montantTotal = ($montant * (1 + $demandeCredit->taux / 100)) + $montantComission;
            $portion_journaliere = ($jours > 0) ? ($montantTotal + $montantComission) / $jours : 0;

            $resultatsInvestisseurs = [[
                'credit_id' => $demandeCredit->id,
                'investisseur_id' => Auth::id(),
                'montant_finance' => $montant,
            ]];

            $creditGrp = credits_groupé::create([
                'emprunteur_id' => $userId,
                'investisseurs' => json_encode($resultatsInvestisseurs),
                'montant' => $montantTotal,
                'montan_restantt' => $montantTotal,
                'taux_interet' => $demandeCredit->taux,
                'date_debut' => $demandeCredit->date_fin,
                'date_fin' => $demandeCredit->duree,
                'portion_journaliere' => $portion_journaliere,
                'comission' => $montantComission,
                'statut' => 'en cours',
                'description' => $demandeCredit->objet_financement,
            ]);

            Remboursements::create([
                'creditGrp_id' => $creditGrp->id,
                'id_user' => Auth::id(),
                'montant_capital' => $montant,
                'montant_interet' => $demandeCredit->taux,
                'date_remboursement' => $demandeCredit->duree,
                'statut' => 'en cours',
                'description' => $demandeCredit->objet_financement,
            ]);

            $createTransaction(Auth::id(), $demandeCredit->id_user, 'Envoie', $montant, $generateReferenceCode(), 'Financement de Crédit d\'achat', 'effectué', $coi->type_compte);
            $createTransaction(Auth::id(), $demandeCredit->id_user, 'Réception', $montant, $generateReferenceCode(), 'Réception de Fonds de Crédit d\'achat', 'effectué', $cfa->type_compte);

            $notification->update(['reponse' => 'approved']);
            $demandeCredit->update(['status' => 'terminer']);

            DB::commit();

            return ['success' => 'Le montant a été ajouté avec succès.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
