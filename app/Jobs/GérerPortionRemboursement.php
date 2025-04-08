<?php

namespace App\Jobs;

use App\Models\Crp;
use App\Models\transactions_remboursement;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PortionJournaliere;
use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GérerPortionRemboursement implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $credit;
    public $reference;
    public $SommeApaye;
    public $portionInteret;
    public $dateDuJour;

    public function __construct($credit, $reference, $SommeApaye, $portionInteret, $dateDuJour)
    {
        $this->credit = $credit;
        $this->reference = $reference;
        $this->SommeApaye = $SommeApaye;
        $this->portionInteret = $portionInteret;
        $this->dateDuJour = $dateDuJour;
    }

    public function handle()
    {
        $credit = $this->credit;

        // Récupération de l'utilisateur et du CRP
        $emprunteur = User::find($credit->emprunteur_id);
        $wallet = Wallet::where('user_id', $credit->emprunteur_id)->first();
        $crp = $wallet->crp;
        $portionCapital = $credit->montant;

        $this->remboursement(
            $credit->id,
            $this->reference,
            $credit->emprunteur_id,
            $credit->emprunteur_id,
            $this->SommeApaye,
            $this->portionInteret,
            $this->dateDuJour,
            'effectué'
        );


        // Préparer notification
        if ($crp && $crp->Solde >= $portionCapital) {
            $message = "Bonne nouvelle ! Vous avez les fonds nécessaires pour votre remboursement de {$portionCapital} FCFA prévu le {$credit->date_fin}.";
            $titre = "Remboursement prêt prêt !";
        } else {
            $message = "Vous devez disposer d'au moins {$portionCapital} FCFA sur votre compte CRP avant le {$credit->date_fin} pour éviter un défaut de paiement.";
            $titre = "Attention : Fonds insuffisants";
        }

        Notification::send($emprunteur, new PortionJournaliere($titre, $message, $credit));
        event(new NotificationSent($emprunteur));
        event(new PortionUpdated($credit->id, $credit->emprunteur_id, $credit->montan_restantt));
    }

    protected function remboursement(int $creditId, int $reference_id, int $emprunteurId, int $investisseurId, float $montant, float $interet, string $date, string $status): void
    {
        $transaction = new transactions_remboursement();
        $transaction->creditGrp_id = $creditId;
        $transaction->reference_id = $reference_id;
        $transaction->emprunteur_id = $emprunteurId;
        $transaction->investisseur_id = $investisseurId;
        $transaction->montant = $montant;
        $transaction->interet = $interet;
        $transaction->date_transaction = $date;
        $transaction->statut = $status;
        $transaction->save();
    }
}
