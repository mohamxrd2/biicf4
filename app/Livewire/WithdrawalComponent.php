<?php

namespace App\Livewire;

use App\Models\Psap;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\gelement;

use App\Models\RetraitRib;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Notifications\Retrait;
use App\Notifications\RetraitCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class WithdrawalComponent extends Component
{

    public $amount;

    public $psap;
    public $amountBank;
    public $bank_account;
    public $bank_account_confirm;
    public $user;
    public $ribs = [];
    public $selected_rib;
    public $showAddNewRib = false;
    public $cle_iban;
    public $code_bic;
    public $code_bank;
    public $code_guiche;
    public $numero_compte;
    public $iban;
    public $bank_name;


    public function mount()
    {
        $this->user = User::find(Auth::id());
        $this->fetchUserRibs();
        $this->resetForm();
    }

    public function fetchUserRibs()
    {
        $this->ribs = RetraitRib::where('id_user', Auth::id())
        ->select('rib', 'bank_name', 'cle_iban', 'code_bic', 'code_bank', 'code_guiche', 'numero_compte', 'iban')
        ->distinct()
        ->get();
    }


    public function toggleAddNewRib()
    {
        $this->showAddNewRib = !$this->showAddNewRib;
        if (!$this->showAddNewRib) {
            $this->bank_account = null;
            $this->bank_account_confirm = null;
        }
    }



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
                        $fail('Le PSAP sélectionné est invalide, non accepté ou correspond à votre propre compte.');
                        Log::warning('PSAP invalide, non accepté, ou identique à l\'utilisateur connecté.', ['psap' => $value, 'user_id' => Auth::id()]);
                    }
                },
            ],
        ], [
            'amount.required' => 'Veuillez indiquer le montant que vous souhaitez retirer.',
            'amount.numeric' => 'Le montant doit être un nombre valide.',
            'amount.min' => 'Le montant minimum pour un retrait est de 1.',
            'psap.required' => 'Veuillez entrer l\'ID ou le nom d\'utilisateur du PSAP.',
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

            if ($this->user->user_joint) {
                $code1 = $this->generateRandomCode();

                // Générer un nouveau code tant qu'il est égal au premier
                do {
                    $code2 = $this->generateRandomCode();
                } while ($code1 === $code2);
            }


            // Logique pour initier le retrait
            $data = [
                'title' => 'Demande de retrait',
                'description' => 'Un utilisateur vous a fait une demande de retrait de ' . $this->amount . ' ���.',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>',
                'code_unique' => $codeUnique,
                'psap' => $psapUser->id,
                'amount' => $this->amount,
                'userId' => Auth::id(),
                'code1' => $code1 ?? null,
                'code2' => $code2 ?? null,
            ];

            Log::info('Creating transaction', ['data' => $data]);

            // Exemple : création de la transaction
            // Transaction::create($data);

            // Envoi de la notification
            Notification::send($psapUser, new Retrait($data));

            if ($this->user->user_joint) {
                $data1 = [
                    'title' => 'Code de confirmation',
                    'description' => 'Veuillez communiquer le code de confirmation au psap pour le retrait',
                    'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                    </svg>',
                    'code_unique' => $this->generateUniqueReference(),
                    'psap' => $psapUser->id,
                    'amount' => $this->amount,
                    'userId' => Auth::id(),
                    'codeRetrait' => $code1,
                ];

                $data2 = [
                    'title' => 'Code de confirmation',
                    'description' => 'Veuillez communiquer le code de confirmation au psap pour le retrait',
                    'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                    </svg>',
                    'code_unique' => $this->generateUniqueReference(),
                    'psap' => $psapUser->id,
                    'amount' => $this->amount,
                    'userId' => Auth::id(),
                    'codeRetrait' => $code2,
                ];

                $userowner = User::find(Auth::id());
                $JointUser = user::find($this->user->user_joint);

                Notification::send($userowner, new RetraitCode($data1));
                Notification::send($JointUser, new RetraitCode($data2));
            }

            DB::commit();
            session()->flash('success', 'Demande de retrait envoyée avec succès.');
            $this->reset();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'initiation du retrait.', ['exception' => $e]);
            session()->flash('error', 'Une erreur est survenue lors de l\'initiation du retrait.');
        }
    }

    public function generateUniqueReference()
    {
        return strtoupper(Str::random(10));
    }

    public function generateRandomCode()
    {
        // Génère un code aléatoire entre 1000 et 9999
        return rand(1000, 9999);
    }
    public function updatedSelectedRib($ribValue)
{
    // Find the selected RIB in the array
    $selectedRib = null;
    foreach ($this->ribs as $rib) {
        if ($rib['rib'] === $ribValue) {
            $selectedRib = $rib;
            break;
        }
    }

    // If no RIB is found, reset all fields
    if (!$selectedRib) {
        $this->reset([
            'bank_account',
            'bank_name',
            'cle_iban',
            'code_bic',
            'code_bank',
            'code_guiche',
            'numero_compte',
            'iban',
        ]);
        return;
    }

    // Try to find the full RIB details in the database
    $recupRib = RetraitRib::where('rib', $selectedRib['rib'])->first();

    // If RIB is found in the database, populate all fields
    if ($recupRib) {
        $this->bank_account = $recupRib->rib;
        $this->bank_name = $recupRib->bank_name;
        $this->cle_iban = $recupRib->cle_iban;
        $this->code_bic = $recupRib->code_bic;
        $this->code_bank = $recupRib->code_bank;
        $this->code_guiche = $recupRib->code_guiche;
        $this->numero_compte = $recupRib->numero_compte;
        $this->iban = $recupRib->iban;
    } 
    // If not found in database, use available information from the array
    else {
        $this->bank_account = $selectedRib['rib'];
        $this->bank_name = $selectedRib['bank_name'] ?? null;
        $this->cle_iban = $selectedRib['cle_iban'] ?? null;
        $this->code_bic = $selectedRib['code_bic'] ?? null;
        $this->code_bank = $selectedRib['code_bank'] ?? null;
        $this->code_guiche = $selectedRib['code_guiche'] ?? null;
        $this->numero_compte = $selectedRib['numero_compte'] ?? null;
        $this->iban = $selectedRib['iban'] ?? null;
    }
}
    public function initiateBankWithdrawal()
    {
        // Si un RIB est sélectionné, l'utiliser comme `bank_account`
        if ($this->selected_rib) {
            $this->bank_account = $this->selected_rib;
        }

        // Validation des champs
        $this->validate([
            'amountBank' => 'required|numeric|min:1|max:50000000',
            'bank_account' => ['required', 'string'],
            'bank_name' => 'required',
            'cle_iban' => 'required|numeric',
            'code_bic' => 'required|numeric',
            'code_bank' => 'required',
            'code_guiche' => 'required',
            'numero_compte' => 'required|numeric',
            'iban' => 'required'
        ], [
            // Messages pour 'amountBank'
            'amountBank.required' => 'Le montant est obligatoire.',
            'amountBank.numeric' => 'Le montant doit être un nombre.',
            'amountBank.min' => 'Le montant doit être au moins :min FCFA.',
            'amountBank.max' => 'Le montant ne peut pas dépasser :max FCFA.',

            // Messages pour 'bank_account'
            'bank_account.required' => 'Le numéro de compte bancaire est obligatoire.',
            'bank_account.string' => 'Le numéro de compte bancaire doit être une chaîne de caractères.',
            'bank_account.regex' => 'Veuillez entrer un RIB valide de 23 chiffres.',

            // Messages pour 'bank_name'
            'bank_name.required' => 'Le nom de la banque est obligatoire.',

            // Messages pour 'cle_iban'
            'cle_iban.required' => 'La clé IBAN est obligatoire.',
            'cle_iban.numeric' => 'La clé IBAN doit être un nombre.',

            // Messages pour 'code_bic'
            'code_bic.required' => 'Le code BIC est obligatoire.',
            'code_bic.numeric' => 'Le code BIC doit être un nombre.',

            // Messages pour 'code_bank'
            'code_bank.required' => 'Le code de la banque est obligatoire.',

            // Messages pour 'code-guiche'
            'code_guiche.required' => 'Le code guichet est obligatoire.',

            // Messages pour 'numero_compte'
            'numero_compte.required' => 'Le numéro de compte est obligatoire.',
            'numero_compte.numeric' => 'Le numéro de compte doit être un nombre.',

            // Messages pour 'iban'
            'iban.required' => 'L\'IBAN est obligatoire.',
        ]);




        // Récupération du portefeuille de l'utilisateur
        $userID = Auth::id();
        $adminId = 1;  // Assurez-vous que l'ID de l'admin est correct
        $userWallet = Wallet::where('user_id', $userID)->first();

        if (!$userWallet) {
            session()->flash('error', 'Portefeuille de l\'utilisateur introuvable.');
            return;
        }

        // Vérification du solde du portefeuille
        if ($userWallet->balance < $this->amountBank) {
            session()->flash('error', 'Solde insuffisant pour effectuer le retrait.');
            return;
        }

        // Génération de la référence unique
        $referenceID = $this->generateIntegerReference();

        // Création du gélement

        $userWallet->balance -= $this->amountBank; // Décrémente le solde du portefeuille
        $userWallet->save(); // Sauvegarde les modifications dans la base de données

        $gelement = Gelement::create([
            'id_wallet' => $userWallet->id,
            'amount' => $this->amountBank,
            'status' => 'En attente',
            'reference_id' => $referenceID,
        ]);

        if ($this->user->user_joint) {
            $code1 = $this->generateRandomCode();

            // Générer un nouveau code tant qu'il est égal au premier
            do {
                $code2 = $this->generateRandomCode();
            } while ($code1 === $code2);
        }
        if ($this->user->user_joint) {
            $data1 = [
                'title' => 'Code de confirmation',
                'description' => 'Veuillez communiquer le code de confirmation au psap pour le retrait',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                </svg>',
                'code_unique' => $this->generateUniqueReference(),
                'psap' => null,
                'amount' => $this->amountBank,
                'userId' => Auth::id(),
                'codeRetrait' => $code1,
            ];

            $data2 = [
                'title' => 'Code de confirmation',
                'description' => 'Veuillez communiquer le code de confirmation au psap pour le retrait',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                </svg>',
                'code_unique' => $this->generateUniqueReference(),
                'psap' => null,
                'amount' => $this->amountBank,
                'userId' => Auth::id(),
                'codeRetrait' => $code2,
            ];

            $userowner = User::find(Auth::id());
            $JointUser = user::find($this->user->user_joint);

            Notification::send($userowner, new RetraitCode($data1));
            Notification::send($JointUser, new RetraitCode($data2));
        }


        // Transaction pour gélement
        $this->createTransaction($userID, $adminId, 'Gele', 'COC', $this->amountBank, $referenceID, 'Retrait par RIB');



        $retrait = RetraitRib::create([
            'id_user' => $userID,
            'amount' => $this->amountBank,
            'rib' => $this->bank_account,
            'reference' => $referenceID,
            'status' => 'En cours',
            'cle_iban' => $this->cle_iban ?? null,
            'code_bic' => $this->code_bic ?? null,
            'code_bank' => $this->code_bank ?? null,
            'code_guiche' => $this->code_guiche ?? null,
            'numero_compte' => $this->numero_compte ?? null,
            'iban' => $this->iban ?? null,
            'bank_name' => $this->bank_name ?? null,
            'code1' => $code1 ?? null,
            'code2' => $code2 ?? null,
        ]);




        $this->resetForm();
        // Recharger la page
        return redirect()->to(request()->header('Referer'));
    }


    protected function createTransaction(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_admin_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    public function resetForm()
    {
        $this->psap = null;
        $this->amount = null;
        $this->amountBank = "";
        $this->bank_account = "";
        $this->selected_rib = "";
    }


    public function render()
    {
        return view('livewire.withdrawal-component', [
            'ribs' => $this->ribs, // Passe les RIB récupérés à la vue
        ]);
    }
}
