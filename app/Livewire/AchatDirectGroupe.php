<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Notification;
use App\Models\AchatDirect as AchatDirectModel;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Events\NotificationSent;
use App\Models\CrediScore;
use App\Models\gelement;
use App\Models\UserPromir;
use App\Notifications\AchatBiicf;
use App\Notifications\Confirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AchatDirectGroupe extends Component
{
    public $produitId;
    public $produit;
    public $userId;
    public $selectedOption = "";
    public $options = [
        'Achat avec livraison',
        'Take Away',
        'Reservation',
    ];
    public $optionsC = [
        'Take Away',
        'Reservation',
    ];
    //
    public $quantité = "";
    public $localite = "";
    public $selectedSpec = false;
    public $userTrader;
    public $nameProd;
    public $userSender;
    public $message = "Un utilisateur veut acheter ce produit";
    public $photoProd;
    public $idProd;
    public $prix;
    public $code_unique;
    public $type;
    public $dateTard;
    public $dateTot;
    public $timeStart;
    public $timeEnd;
    public $dayPeriod = "";
    public $dayPeriodFin = "";

    public $userBalance;
    public $totalCost;
    public $isButtonDisabled = false;
    public $isButtonHidden = false;
    public $currentPage = 'achat';
    public $errorMessage = ''; // Add this property
    protected $listeners = ['navigate' => 'setPage'];
    public function setPage($page)
    {
        $this->currentPage = $page;
    }

    public function mount($id)
    {
        $this->produitId = $id;
        $this->produit = ProduitService::findOrFail($id);
        $this->userId = Auth::guard('web')->id();
        $this->nameProd = $this->produit->name;
        $this->type = $this->produit->type;
        $this->userSender = $this->userId;
        $this->userTrader = $this->produit->user->id;
        $this->photoProd = $this->produit->photoProd1;
        $this->idProd = $this->produit->id;
        $this->prix = $this->produit->prix;
        $this->selectedOption = '';  // Initialiser la valeur de l'option sélectionnée
        // Récupérer l'identifiant de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer le portefeuille de l'utilisateur
        $userWallet = Wallet::where('user_id', $userId)->first();

        // Assume user balance is fetched from the authenticated user
        $this->userBalance = $userWallet ?? 0;
        $this->totalCost = (int)$this->quantité * $this->prix;
    }
    public function updatedQuantité()
    {

        $this->totalCost = (int)$this->quantité * $this->prix;

        // Vérification des conditions selon le type
        if ($this->type === 'Produit') {
            $qteMin = $this->produit->qteProd_min;
            $qteMax = $this->produit->qteProd_max;

            if ($this->quantité < $qteMin || $this->quantité > $qteMax) {
                $this->errorMessage = "La quantité doit être comprise entre {$qteMin} et {$qteMax}.";
                $this->isButtonHidden = false;
                $this->isButtonDisabled = true;
                return; // Arrêter l'exécution ici, car les autres vérifications ne sont pas nécessaires
            }
        }

        // Vérification du solde utilisateur
        if ($this->totalCost > $this->userBalance->balance) {
            $solde = $this->userBalance->balance;
            $this->errorMessage = "Vous n'avez pas assez de fonds pour procéder. Votre solde est : {$solde} FCFA.";
            $this->isButtonHidden = true;
            $this->isButtonDisabled = true;
        } else {
            // Si toutes les vérifications passent
            $this->errorMessage = ''; // Clear the error message
            $this->isButtonHidden = false;
            $this->isButtonDisabled = false;
        }
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function AchatDirectForm()
    {
        // Valider les données
        $validated = $this->validateData();

        if ($validated === false) {
            return; // Arrête l'exécution si la validation échoue
        }

        $userId = Auth::id();
        if (!$userId) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $montantTotal = $this->totalCost;
        $userWallet = $this->getUserWallet($userId);

        $this->updatedQuantité();
        // Vérifier si l'option sélectionnée est vide
        if (empty($this->selectedOption)) {
            $this->addError('selectedOption', 'Vous devez sélectionner une option de réception.');
        }
        // Commencer la transaction
        DB::beginTransaction();
        try {
            $codeUnique = $this->generateUniqueReference();
            if (!$codeUnique) {
                throw new \Exception('Code unique non généré.');
            }

            // Traiter l'achat
            $achat = $this->createPurchase($validated, $montantTotal, $codeUnique);
            // Vérification de l'existence de l'achat dans les transactions gelées
            gelement::create([
                'reference_id' => $codeUnique,
                'id_wallet' => $userWallet->id,
                'amount' => $montantTotal,
            ]);

            // Mettre à jour le portefeuille
            $this->updateWalletBalance($userWallet, $montantTotal);

            // Créer les transactions
            $reference_id = $this->generateIntegerReference();
            $this->createTransaction(
                $userId,
                $validated['userTrader'],
                'Gele',
                $montantTotal,
                $reference_id,
                'Gele Pour Achat de ' . $validated['nameProd'],
                'effectué',
                'COC'
            );

            // Gérer les notifications
            $this->sendNotifications($validated, $achat, $codeUnique);

            DB::commit();

            // Réinitialiser les champs du formulaire
            $this->resetForm();

            // Émettre un événement de succès
            $this->dispatch('formSubmitted', 'Achat Affectué Avec Succès');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue.');
        }
    }

    private function validateData()
    {
        $validated = $this->validate([
            'quantité' => 'required|integer',
            'localite' => 'required|string|max:255',
            'selectedOption' => 'required|string',
            'dateTot' => $this->selectedOption === 'Take Away' ? 'required|date' : 'nullable|date',
            'dateTard' => 'nullable|date',
            'timeStart' => 'nullable|date_format:H:i',
            'timeEnd' => 'nullable|date_format:H:i',
            'dayPeriod' => 'nullable|string',
            'dayPeriodFin' => 'nullable|string',
            'userTrader' => 'required|exists:users,id',
            'nameProd' => 'required|string',
            'userSender' => 'required|exists:users,id',
            'photoProd' => 'required|string',
            'idProd' => 'required|exists:produit_services,id',
            'prix' => 'required|numeric',
        ]);

        if ($this->selectedOption === 'Take Away' && $this->type == 'Produit') {
            if (!$this->validateTimeStartAndDayPeriod()) {
                // Si la validation échoue, on retourne false pour bloquer la suite
                return false;
            }
        }

        return $validated;
    }

    private function validateTimeStartAndDayPeriod()
    {
        // Check if either timeStart or dayPeriod is filled (but not both)
        $hasTimeStart = !empty($this->timeStart);
        $hasDayPeriod = !empty($this->dayPeriod);

        if (!$hasTimeStart && !$hasDayPeriod) {
            $this->addError('time', 'Vous devez remplir soit Heure de début soit Période.');
            return false;
        }

        if ($hasTimeStart && $hasDayPeriod) {
            $this->addError('time', 'Vous ne pouvez pas remplir les deux champs en même temps.');
            return false;
        }

        $this->resetErrorBag(['timeStart', 'dayPeriod']);
        return true;
    }

    private function getUserWallet($userId)
    {
        return Wallet::where('user_id', $userId)->first();
    }

    private function updateWalletBalance($userWallet, $montantTotal)
    {
        $userWallet->decrement('balance', $montantTotal);
    }

    private function createPurchase($validated, $montantTotal, $codeUnique)
    {
        return AchatDirectModel::create([
            'nameProd' => $validated['nameProd'],
            'quantité' => $validated['quantité'],
            'montantTotal' => $montantTotal,
            'type_achat' => 'achatDirect',
            'localite' => $validated['localite'],
            'date_tot' => $validated['dateTot'],
            'date_tard' => $validated['dateTard'],
            'timeStart' => $validated['timeStart'],
            'timeEnd' => $validated['timeEnd'],
            'dayPeriod' => $validated['dayPeriod'],
            'dayPeriodFin' => $validated['dayPeriodFin'],
            'userTrader' => $validated['userTrader'],
            'userSender' => $validated['userSender'],
            'specificite' => $this->produit->specification,
            'photoProd' => $validated['photoProd'],
            'idProd' => $validated['idProd'],
            'code_unique' => $codeUnique,
        ]);
    }

    private function sendNotifications($validated, $achat, $codeUnique)
    {
        $userConnecte = User::find($validated['userSender']);
        Notification::send($userConnecte, new Confirmation([
            'nameProd' => $validated['nameProd'],
            'idProd' => $validated['idProd'],
            'code_unique' => $codeUnique,
            'idAchat' => $achat->id,
            'title' => 'Commande effectuée avec succès',
            'description' => 'Cliquez pour voir les détails de votre commande.',
        ]));

        // $this->dispatch('refreshNotifications');

        $achatUser = [
            'nameProd' => $validated['nameProd'],
            'idProd' => $validated['idProd'],
            'code_unique' => $codeUnique,
            'idAchat' => $achat->id,
            'title' => 'Nouvelle commande',
            'description' => 'Veuillez vérifier si le produit est disponible.',
        ];

        $owner = User::find($validated['userTrader']);
        Notification::send($owner, new AchatBiicf($achatUser));
        event(new NotificationSent($owner));

        // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
        $notification = $owner->notifications()->where('type', AchatBiicf::class)->latest()->first();

        if ($notification) {
            if ($this->selectedOption === 'Take Away') {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => 'Take Away']);
            } else {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => 'Delivery']);
            }
        }
    }

    private function resetForm()
    {
        $this->reset(['quantité', 'localite']);
    }


    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status,  string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->type_compte = $type_compte;
        $transaction->status = $status;
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    public function credit()
    {
        // $this->dispatch('navigate', 'credit');

        // Récupérer l'utilisateur actuellement connec
        $user = auth()->user();
        $userNumber = $user->phone;

        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $userInPromir = UserPromir::where('numero', $userNumber)->first();

        if ($userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $crediScore = CrediScore::where('id_user', $userInPromir->id)->first();

            if ($crediScore) {
                // Vérifier si le score est A+, A, ou A-
                if (in_array($crediScore->ccc, ['A+', 'A', 'A-'])) {
                    $this->dispatch(
                        'formSubmitted',
                        'Votre numéro existe dans Promir et votre score de crédit est ' . $crediScore->ccc . ', Alors vous etes éligible au credit'
                    );
                    $this->checkEligibility();
                } else {
                    $this->dispatch(
                        'formSubmitted',
                        'Votre numéro existe dans Promir, mais votre score de crédit est ' . $crediScore->ccc . ', ce qui n\'est pas éligible.'
                    );
                }
            } else {
                $this->dispatch(
                    'formSubmitted',
                    'Votre numéro existe dans Promir, mais aucun score de crédit n\'a été trouvé.'
                );
            }
        } else {
            // L'utilisateur n'existe pas dans user_promir, afficher un message d'erreur
            $this->dispatch(
                'formSubmitted',
                'Votre numéro n\'existe pas dans la base de données Promir. Vous n\'etes pas eligible.'
            );
        }
    }

    public function checkEligibility()
    {
        // Logique de vérification ici...
        $isEligible = true; // Votre logique d'éligibilité ici
        $quantiteMax = $this->produit->qteProd_max; // Votre logique d'éligibilité ici
        $quantiteMin = $this->produit->qteProd_min; // Votre logique d'éligibilité ici
        $prix = $this->produit->prix; // Votre logique d'éligibilité ici
        $montantmax = $prix * $quantiteMax;
        $nameProd = $this->produit->name;

        // Émettre l'événement si l'utilisateur est éligible
        if ($isEligible) {
            $this->dispatch('navigate', 'credit');
        }
    }



    public function render()
    {
        // Récupérer le produit ou échouer
        $produit = ProduitService::findOrFail($this->produitId);

        // Récupérer l'identifiant de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer le portefeuille de l'utilisateur
        $userWallet = Wallet::where('user_id', $userId)->first();

        return view('livewire.achat-direct-groupe', compact(
            'produit',
            'userWallet',
            'userId',
        ));
    }
}
