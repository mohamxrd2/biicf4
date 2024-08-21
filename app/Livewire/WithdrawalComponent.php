<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Transaction;
use App\Notifications\Retrait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class WithdrawalComponent extends Component
{

    public $amount;

    public $psap;
    public $formVisible = false;

    public function showForm()
    {
        $this->formVisible = true;
    }
    public function initiateWithdrawal()
    {
        Log::info('Initiating withdrawal', ['amount' => $this->amount, 'psap' => $this->psap]);

        $this->validate([
            'amount' => 'required|numeric|min:1',
            'psap' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::where('id', $value)->orWhere('username', $value)->exists()) {
                        $fail('Le PSAP sélectionné est invalide.');
                        Log::warning('Invalid PSAP provided', ['psap' => $value]);
                    }
                },
            ],
        ]);

        DB::beginTransaction();

        try {
            Log::info('Searching for PSAP user', ['psap' => $this->psap]);

            // Recherche de l'utilisateur correspondant à $psap
            $psapUser = User::where('id', $this->psap)
                            ->orWhere('username', $this->psap)
                            ->firstOrFail();

            Log::info('PSAP user found', ['psapUser' => $psapUser->id]);

            // Logique pour initier le retrait
            $data = [
                'psap' => $psapUser->id,
                'amount' => $this->amount,
                'userId' => Auth::id(),
            ];
            

            Log::info('Creating transaction', ['data' => $data]);

            // Exemple : création de la transaction
            // Transaction::create($data);

            // Envoi de la notification
            Notification::send($psapUser, new Retrait($data));

            Log::info('Notification sent to PSAP user', ['psapUser' => $psapUser->id]);

            DB::commit(); // Validation de la transaction

            Log::info('Transaction committed successfully');

            // Réinitialisation de l'état du composant
            $this->formVisible = false;
            $this->psap = null;
            $this->amount = null;

            session()->flash('message', 'Demande de retrait effectuée.');
        } catch (\Exception $e) {
            DB::rollBack(); // Annulation de la transaction en cas d'erreur
            Log::error('Error during withdrawal initiation', ['error' => $e->getMessage()]);

            session()->flash('error', 'Une erreur est survenue lors de l\'initiation du retrait.');
        }
    }

    public function render()
    {
        return view('livewire.withdrawal-component');
    }
}
