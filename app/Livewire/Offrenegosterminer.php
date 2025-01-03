<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\AchatBiicf;
use App\Notifications\Confirmation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Str;

class Offrenegosterminer extends Component
{
    public $notification;
    public $id;
    public $produitId;
    public $produit;
    public $userId;
    public $prixProd;
    public $quantité;
    public $quantite;
    public $type;
    public $idProd;
    public $photo;
    public $selectedOption = "";

    //
    public $dayPeriodFin;
    public $nameProd;
    public $localite;
    public $userTrader;
    public $userSender;
    public $selectedSpec = false;
    public $code_unique;
    public $dateTard;
    public $dateTot;
    public $photoProd;
    public $timeStart;
    public $timeEnd;
    public $prix;
    public $dayPeriod = "";

    public function mount($id)
    {

        $this->notification = DatabaseNotification::findOrFail($id);

        $this->produit = ProduitService::findOrFail($this->notification->data['idProd']);
        $this->userId = Auth::guard('web')->id();
        $this->nameProd = $this->produit->name;
        $this->type = $this->produit->type;
        $this->userSender = $this->userId;
        $this->userTrader = $this->produit->user->id;
        $this->photoProd = $this->produit->photoProd1;
        $this->idProd = $this->produit->id;
        $this->prix = $this->notification->data['prixTrade'];
        $this->selectedOption = '';  // Initialiser la valeur de l'option sélectionnée

    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function AchatDirectForm()
    {

        $validated = $this->validate([
            'quantité' => 'required|integer',
            'localite' => 'required|string|max:255',
            'selectedOption' => 'required|string',
            'dateTot' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
            'dateTard' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
            'timeStart' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
            'timeEnd' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
            'dayPeriod' => $this->selectedOption == 'Take Away' ? 'nullable|string' : 'nullable|string',
            'dayPeriodFin' => $this->selectedOption == 'Take Away' ? 'nullable|string' : 'nullable|string',
            'userTrader' => 'required|exists:users,id',
            'nameProd' => 'required|string',
            'userSender' => 'required|exists:users,id',
            'photoProd' => 'required|string',
            'idProd' => 'required|exists:produit_services,id',
            'prix' => 'required|numeric',
        ]);

        // dd($validated);

        Log::info('Validation réussie.', $validated);

        $userId = Auth::id();
        $montantTotal = $validated['quantité'] * $this->prix;

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

        ($codeUnique = $this->generateUniqueReference());
        if (!$codeUnique) {
            Log::error('Code unique non généré.');
            throw new \Exception('Code unique non généré.');
        }

        // // Commencez une transaction de base de données
        DB::beginTransaction();
        try {
            // Mettre à jour le solde du portefeuille
            $userWallet->decrement('balance', $montantTotal);

            // Générer une référence de transaction
            $reference_id = $this->generateIntegerReference();

            // Mettre à jour la table de AchatDirectModel de fond
            $achat = AchatDirect::create([
                'nameProd' => $validated['nameProd'],
                'prix' => $this->notification->data['prixTrade'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montantTotal,
                'localite' => $validated['localite'],
                'date_tot' => $validated['dateTot'],
                'date_tard' => $validated['dateTard'],
                'timeStart' => $validated['timeStart'],
                'timeEnd' => $validated['timeEnd'],
                'type_achat' => 'achatDirect',
                'dayPeriod' => $validated['dayPeriod'],
                'dayPeriodFin' => $validated['dayPeriodFin'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $this->produit->specification,
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
                'code_unique' => $codeUnique, // Utiliser la variable vérifiée

            ]);

            // Mettre à jour la table de gelement de fond
            gelement::create([
                'id_wallet' => $userWallet->id,
                'amount' => $montantTotal,
                'reference_id' => $codeUnique,
            ]);

            // Mettre à jour la table de AchatDirectModel de fond
            $achatUser = [
                'nameProd' => $validated['nameProd'],
                'idProd' => $validated['idProd'],
                'code_unique' => $codeUnique,
                'idAchat' => $achat->id,
                'title' => 'Confirmation de commande',
                'description' => 'La commande a été envoyéé avec success.',
            ];

            // Créer  transactions
            $this->createTransaction($userId, $validated['userTrader'], 'Gele', $montantTotal, $reference_id, 'Gele Pour ' . 'Achat de ' . $validated['nameProd'], 'effectué', 'COC');

            $owner = User::find($validated['userTrader']);
            Notification::send($owner, new AchatBiicf($achatUser));

            // Après l'envoi de la notification
            event(new NotificationSent($owner));

            
            $this->reset(['quantité', 'localite']);

            $userConnecte = Auth::user(); // Récupérer l'utilisateur connecté

            if ($userConnecte instanceof User) { // Vérifier que c'est un utilisateur valide

                //Envoyer une notification au propriétaire ($owner)
                Notification::send($userConnecte, new Confirmation($achatUser));

                // Déclencher un événement pour signaler l'envoi de la notification
                event(new NotificationSent($userConnecte));
            }
            // Émettre un événement après la soumission
            $this->dispatch('formSubmitted', 'Achat Affectué Avec Success');
            // Valider la transaction de base de données
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue ');
        }
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


    public function render()
    {
        // Récupérer le produit ou échouer
        $produit = ProduitService::findOrFail($this->notification->data['idProd']);

        // Récupérer l'identifiant de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer le portefeuille de l'utilisateur
        $userWallet = Wallet::where('user_id', $userId)->first();


        return view(
            'livewire.offrenegosterminer',
            compact(
                'produit',
                'userWallet',
                'userId',
            )
        );
    }
}
