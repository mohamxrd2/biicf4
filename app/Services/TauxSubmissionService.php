<?php

namespace App\Services;

use App\Models\AjoutMontant;
use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\gelement;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Events\CommentSubmittedTaux;

class TauxSubmissionService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function handleCommentForm($demandeCredit, $userConnecte, $wallet, $coi, $tauxTrade, $userId)
    {
        // Valider solde & ajoutMontant
        $ajoutMontant = AjoutMontant::where('id_demnd_credit', $demandeCredit->id)
            ->where('id_invest', $userConnecte)
            ->first();

        if (!$ajoutMontant) {
            if ($coi && $coi->Solde < $demandeCredit->montant) {
                Session::flash('error', 'Votre solde est insuffisant pour soumettre une offre. Montant requis : ' . $demandeCredit->montant . ' CFA.');
                return false;
            }

            $this->confirmer2($demandeCredit, $wallet, $coi);
        }

        $this->elementCommentForm($demandeCredit, $tauxTrade, $userId);

        return true;
    }

    public function confirmer2($demandeCredit, $wallet, $coi)
    {
        DB::beginTransaction();

        try {
            AjoutMontant::create([
                'montant' => $demandeCredit->montant,
                'id_invest' => Auth::id(),
                'id_emp' => $demandeCredit->id_user,
                'id_demnd_credit' => $demandeCredit->id,
            ]);

            gelement::create([
                'id_wallet' => $wallet->id,
                'amount' => $demandeCredit->montant,
                'reference_id' => $demandeCredit->demande_id,
            ]);

            if ($coi) {
                $coi->Solde -= $demandeCredit->montant;
                $coi->save();
            }

            $this->transactionService->createTransaction(
                Auth::id(),
                $demandeCredit->id_user,
                'Gele',
                $demandeCredit->montant,
                $this->generateIntegerReference(),
                'financement  de credit d\'achat',
                $coi->type_compte
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
        }
    }

    public function elementCommentForm($demandeCredit, $tauxTrade, $userId)
    {
        DB::beginTransaction();

        try {
            $commentTaux = CommentTaux::create([
                'taux' => $tauxTrade,
                'code_unique' => $demandeCredit->demande_id,
                'id_invest' => Auth::id(),
                'id_emp' => $demandeCredit->id_user,
            ]);

            broadcast(new CommentSubmittedTaux($tauxTrade, $commentTaux->id))->toOthers();

            DB::commit();
            Session::flash('message', 'Commentaire sur le taux ajouté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }

        // Créer compte à rebours si inexistant
        $existingCountdown = Countdown::where('code_unique', $demandeCredit->demande_id)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $userId,
                'start_time' => now(),
                'difference' => 'credit_taux',
                'code_unique' => $demandeCredit->demande_id,
            ]);
        }
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
}
