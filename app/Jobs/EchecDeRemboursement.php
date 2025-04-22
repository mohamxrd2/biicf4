<?php

namespace App\Jobs;

use App\Events\NotificationSent;
use App\Models\credits_groupé;
use App\Models\User;
use App\Notifications\PortionJournaliere;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EchecDeRemboursement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?credits_groupé $credit;
    protected ?float $portionCapital;
    protected ?float $portionInteret;
    protected string $message;

    public function __construct(?credits_groupé $credit = null, ?float $portionCapital = null, ?float $portionInteret = null, string $message = '')
    {
        $this->credit = $credit;
        $this->portionCapital = $portionCapital;
        $this->portionInteret = $portionInteret;
        $this->message = $message;
    }

    public function handle(): void
    {
        if (!$this->credit) {
            throw new Exception("Crédit non fourni.");
        }

        $emprunteur = User::find($this->credit->emprunteur_id);

        if (!$emprunteur) {
            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $this->credit->id);
        }

        $echec = 'Échec de remboursement';

        Notification::send($emprunteur, new PortionJournaliere(
            $echec,
            $this->message,
            $this->credit
        ));

        event(new NotificationSent($emprunteur));
    }
}
