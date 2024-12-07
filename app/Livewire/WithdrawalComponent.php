<?php

namespace App\Livewire;

use App\Models\Psap;
use App\Models\User;
use Livewire\Component;
use App\Models\Transaction;

use Illuminate\Support\Str;
use App\Notifications\Retrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class WithdrawalComponent extends Component
{

    public $amount;

    public $psap;


    public function initiateWithdrawal()
    {
        Log::info('Initiating withdrawal', ['amount' => $this->amount, 'psap' => $this->psap]);

        $this->validate([
            'amount' => 'required|numeric|min:1',
            'psap' => [
                'required',
                function ($attribute, $value, $fail) {
                    $psap = Psap::where('user_id', $value)
                                ->where('etat', 'Accepté')
                                ->where('user_id', '!=', Auth::id())
                                ->first();
        
                    if (!$psap) {
                        $fail('Le PSAP sélectionné est invalide, non accepté, ou correspond à votre propre compte.');
                        Log::warning('Invalid, unaccepted, or self PSAP provided', ['psap' => $value, 'user_id' => Auth::id()]);
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

            ($codeUnique = $this->generateUniqueReference());
            if (!$codeUnique) {
                Log::error('Code unique non généré.');
                throw new \Exception('Code unique non généré.');
            }
    

            // Logique pour initier le retrait
            $data = [
                'title' => 'Demande de retrait',
                'description' => 'Un utilisateur vous a fait une demande de retrait de ' . $this->amount. ' ���.',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
</svg>
',
                'code_unique' => $codeUnique,
                
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
            $this->psap = null;
            $this->amount = null;

            session()->flash('message', 'Demande de retrait envoyée.');
        } catch (\Exception $e) {
            DB::rollBack(); // Annulation de la transaction en cas d'erreur
            Log::error('Error during withdrawal initiation', ['error' => $e->getMessage()]);

            session()->flash('error', 'Une erreur est survenue lors de l\'initiation du retrait.');
        }
    }

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function render()
    {
        return view('livewire.withdrawal-component');
    }
}
