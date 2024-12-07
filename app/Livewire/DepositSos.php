<?php

namespace App\Livewire;

use App\Models\Coi;
use App\Models\Cfa;
use App\Models\User;
use App\Models\Wallet;
use App\Models\RechargeSos;
use App\Models\Transaction;
use App\Notifications\DepositRecu;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;
use Illuminate\Support\Str;

class DepositSos extends Component
{
    public $roiDeposit;
    public $amountDeposit;
    public $operator;
    public $phonenumber;
    public $existingRequest;
    public $notification;
    public $id_sos;
    public $userDeposit;

    public function mount($id)
    {
        $this->resetForm();
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->roiDeposit = $this->notification->data['roi'];
        $this->amountDeposit = $this->notification->data['amount'];
        $this->userDeposit = User::find($this->notification->data['user_id']);
        $this->id_sos = $this->notification->data['id_sos'] ?? null;
        $this->existingRequest = RechargeSos::where('id_sos', $this->id_sos)->first();
    }

    public function acceptDeposit()
    {
        try {
            Log::info("Début de l'acceptation de la demande avec ID notification : {$this->notification->id}");

            $this->validate([
                'amountDeposit' => 'required|numeric|min:1',
                'roiDeposit' => 'required|numeric|min:1',
                'operator' => 'required|string',
                'phonenumber' => 'required|numeric',
            ], [
                'amountDeposit.required' => 'Veuillez entrer un montant de dépôt.',
                'amountDeposit.numeric' => 'Le montant de dépôt doit être un nombre.',
                'amountDeposit.min' => 'Le montant de dépôt doit être supérieur à zéro.',
                'roiDeposit.required' => 'Veuillez entrer un ROI.',
                'roiDeposit.numeric' => 'Le ROI doit être un nombre.',
                'roiDeposit.min' => 'Le ROI doit être supérieur à zéro.',
                'operator.required' => 'Veuillez sélectionner un opérateur.',
                'phonenumber.required' => 'Veuillez entrer un numéro de téléphone.',
            ]);

            if ($this->existingRequest) {
                session()->flash('error', 'La demande est expirée. Un utilisateur a déjà accepté la demande.');
                return;
            }

            $investWallet = Wallet::where('user_id', Auth::id())->first();
            if (!$investWallet) {
                session()->flash('error', 'Wallet non trouvé pour l’utilisateur.');
                return;
            }

            $investCoi = Coi::where('id_wallet', $investWallet->id)->first();
            if (!$investCoi) {
                session()->flash('error', 'Coi non trouvé pour le wallet.');
                return;
            }

            $demWallet = Wallet::where('user_id', $this->notification->data['user_id'])->first();
            if (!$demWallet) {
                session()->flash('error', 'Wallet non trouvé pour l’utilisateur.');
                return;
            }

            $demCfa = Cfa::where('id_wallet', $demWallet->id)->first();
            if (!$demCfa) {
                session()->flash('error', 'Cfa non trouvé pour le wallet.');
                return;
            }

            $investCoi->decrement('Solde', $this->amountDeposit);
            $demCfa->increment('Solde', $this->amountDeposit);

            $referenceId = $this->generateIntegerReference();

            $this->createTransaction(
                Auth::id(),
                $this->notification->data['user_id'],
                'Envoie',
                $this->amountDeposit,
                $referenceId,
                'Rechargement SOS',
                'effectué',
                'COC'
            );

            $this->createTransaction(
                Auth::id(),
                $this->notification->data['user_id'],
                'Réception',
                $this->amountDeposit,
                $referenceId,
                'Rechargement SOS',
                'effectué',
                'CFA'
            );

            ($codeUnique = $this->generateUniqueReference());
            if (!$codeUnique) {
                Log::error('Code unique non généré.');
                throw new \Exception('Code unique non généré.');
            }

            Notification::send(User::find($this->notification->data['user_id']), new DepositRecu([
                'title' => 'Recu pour rechargement SOS',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.621 9.879a3 3 0 0 0-5.02 2.897l.164.609a4.5 4.5 0 0 1-.108 2.676l-.157.439.44-.22a2.863 2.863 0 0 1 2.185-.155c.72.24 1.507.184 2.186-.155L15 18M8.25 15.75H12m-1.5-13.5H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>
',
                'description' => 'Veuillez consulter le recu de votre de demande de rechargement SOS',
                'code_unique' => $codeUnique ,
                'user_id' => Auth::id(),
                'amount' => $this->amountDeposit,
                'roi' => $this->roiDeposit,
                'id_sos' => $this->id_sos,
                'phonenumber' => $this->phonenumber,
                'operator' => $this->operator,
            ]));

            RechargeSos::create([
                'userdem' => $this->notification->data['user_id'],
                'userinvest' => Auth::id(),
                'montant' => $this->amountDeposit,
                'roi' => $this->roiDeposit,
                'operator' => $this->operator,
                'phone' => $this->phonenumber,
                'id_sos' => $this->id_sos,
                'statut' => 'accepté',
            ]);

            $this->notification->update(['reponse' => 'Accepté']);
            session()->flash('success', 'La demande de dépôt a été acceptée avec succès.');
            $this->resetForm();
        } catch (\Exception $e) {
            Log::error('Erreur lors de l’acceptation de la demande de dépôt : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l’acceptation : ' . $e->getMessage());
        }
    }

    public function rejectDeposit()
    {
        $this->notification->update(['reponse' => 'Refusé']);
        session()->flash('success', 'La demande de dépôt a été refusée avec succès.');
        $this->resetForm();
    }

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }


    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte): void
    {
        Transaction::create([
            'sender_user_id' => $senderId,
            'receiver_user_id' => $receiverId,
            'type' => $type,
            'amount' => $amount,
            'reference_id' => $reference_id,
            'description' => $description,
            'type_compte' => $type_compte,
            'status' => $status,
        ]);
    }

    protected function generateIntegerReference(): int
    {
        return (int) (microtime(true) * 1000);
    }

    public function resetForm()
    {
        $this->operator = '';
        $this->phonenumber = '';
    }

    public function render()
    {
        return view('livewire.deposit-sos');
    }
}
