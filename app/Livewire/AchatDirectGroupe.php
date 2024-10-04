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
use App\Events\MyEvent;
use App\Events\NotificationSent;
use App\Models\AchatGrouper;
use App\Models\Consommation;
use App\Models\CrediScore;
use App\Models\NotificationLog;
use App\Models\UserPromir;
use App\Notifications\AchatBiicf;
use App\Notifications\AchatGroupBiicf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
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
        $this->code_unique = $this->generateUniqueReference();
        $this->selectedOption = '';  // Initialiser la valeur de l'option sélectionnée



    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function AchatDirectForm()
    {
        $validated = $this->validate([
            'nameProd' => 'required|string',
            'quantité' => 'required|integer',
            'prix' => 'required|numeric',
            'selectedOption' => 'required|string',
            'dateTot' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
            'dateTard' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
            'timeStart' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
            'timeEnd' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
            'dayPeriod' => $this->selectedOption == 'Take Away' ? 'nullable|string' : 'nullable|string',
            'localite' => 'required|string|max:255',
            'userTrader' => 'required|exists:users,id',
            'userSender' => 'required|exists:users,id',
            'photoProd' => 'required|string',
            'idProd' => 'required|exists:produit_services,id',
        ]);



        Log::info('Validation réussie.', $validated);

        $userId = Auth::id();
        $montantTotal = $validated['quantité'] * $validated['prix'];

        if (!$userId) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id', $userId)->first();

        if (!$userWallet) {
            Log::error('Portefeuille introuvable.', ['userId' => $userId]);
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        if ($userWallet->balance < $montantTotal) {
            Log::warning('Fonds insuffisants pour effectuer cet achat.', [
                'userId' => $userId,
                'requiredAmount' => $montantTotal,
                'walletBalance' => $userWallet->balance,
            ]);
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        try {
            // Utilisez `selectedSpec` pour obtenir la spécification sélectionnée
            $selectedSpec = $this->selectedSpec;

            // Assurez-vous que `selectedSpec` est bien défini
            $specificites = !empty($selectedSpec) ? $selectedSpec : null;

            $achat = AchatDirectModel::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montantTotal,
                'localite' => $validated['localite'],
                'date_tot' => $validated['dateTot'],
                'date_tard' => $validated['dateTard'],
                'timeStart' => $validated['timeStart'],
                'timeEnd' => $validated['timeEnd'],
                'dayPeriod' => $validated['dayPeriod'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $specificites,
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
                'code_unique' => $this->code_unique,

            ]);
            // dd($achat);
            $userWallet->decrement('balance', $montantTotal);

            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $montantTotal;
            $transaction->save();

            Log::info('Transaction enregistrée.', [
                'transactionId' => $transaction->id,
                'amount' => $montantTotal,
            ]);

            $owner = User::find($validated['userTrader']);
            $selectedOption = $this->selectedOption;
            Notification::send($owner, new AchatBiicf($achat));
            // Après l'envoi de la notification
            event(new NotificationSent($owner));

            // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
            $notification = $owner->notifications()->where('type', AchatBiicf::class)->latest()->first();

            if ($notification) {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => $selectedOption]);
            }

            $user = User::find($userId);
            $this->reset(['quantité', 'localite', 'selectedSpec']);
            session()->flash('success', 'Achat passé avec succès.');
            // Émettre un événement après la soumission
            $this->dispatch('formSubmitted', 'achat effectué avec success');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }


    public function requestCredit()
    {
        // Récupérer l'utilisateur actuellement connec
        $user = auth()->user();
        $userNumber = $user->phone;

        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $userInPromir = UserPromir::where('numero', $userNumber)->exists();

        if ($userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $crediScore = CrediScore::where('id_user', $userInPromir)->first();

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

        // Émettre l'événement si l'utilisateur est éligible
        if ($isEligible) {
            $this->dispatch('userIsEligible', $isEligible);
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

        // Récupérer les IDs des propriétaires des consommations similaires
        $idsProprietaires = Consommation::where('name', $produit->name)
            ->where('id_user', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // Compter le nombre d'IDs distincts
        $nombreProprietaires = count($idsProprietaires);

        // Récupérer les fournisseurs pour ce produit
        $nomFournisseur = ProduitService::where('name', $produit->name)
            ->where('user_id', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        $nomFournisseurCount = count($nomFournisseur);

        // Récupérer le nombre d'achats groupés distincts pour ce produit
        $nbreAchatGroup = AchatGrouper::where('idProd', $produit->id)
            ->distinct('userSender')
            ->count('userSender');

        // Récupérer la date la plus ancienne parmi les achats groupés pour ce produit
        $datePlusAncienne = AchatGrouper::where('idProd', $produit->id)->min('created_at');
        $tempsEcoule = $datePlusAncienne ? Carbon::parse($datePlusAncienne)->addMinutes(1) : null;


        // $this->verifierEtEnvoyerNotification();

        return view('livewire.achat-direct-groupe', compact(
            'produit',
            'userWallet',
            'userId',
            'nbreAchatGroup',
            'datePlusAncienne',
            'idsProprietaires',
            'nombreProprietaires',
            'nomFournisseur',
            'nomFournisseurCount',
        ));
    }
}
