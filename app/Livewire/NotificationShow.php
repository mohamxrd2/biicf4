<?php

namespace App\Livewire;

use Exception;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Comment;
use Livewire\Component;
use App\Models\Countdown;
use App\Models\Livraisons;
use App\Models\AchatDirect;
use App\Models\OffreGroupe;
use App\Models\RechargeSos;
use App\Models\Transaction;
use Livewire\Attributes\On;
use App\Models\Consommation;
use App\Models\groupagefact;
use App\Models\userquantites;
use App\Rules\ArrayOrInteger;

use Livewire\WithFileUploads;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use Illuminate\Support\Carbon;
use App\Models\NotificationLog;
use App\Notifications\mainleve;
use App\Events\CommentSubmitted;
use App\Notifications\VerifUser;
use App\Models\AppelOffreGrouper;
use App\Notifications\AchatBiicf;
use App\Notifications\AppelOffre;
use App\Notifications\RefusAchat;
use App\Notifications\RefusVerif;

use App\Events\AjoutQuantiteOffre;
use App\Models\ComissionAdmin;
use App\Models\gelement;
use App\Notifications\acceptAchat;
use App\Notifications\Confirmation;
use App\Notifications\DepositRecu;
use App\Notifications\DepositSend;
use Illuminate\Support\Facades\DB;
use App\Notifications\commandVerif;
use App\Notifications\mainlevefour;
use App\Notifications\RefusRetrait;
use Illuminate\Support\Facades\Log;
use App\Notifications\AcceptRetrait;
use App\Notifications\AllerChercher;
use App\Notifications\attenteclient;
use App\Notifications\NegosTerminer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Notifications\commandVerifag;
use App\Notifications\livraisonVerif;
use App\Notifications\mainleveclient;
use App\Notifications\OffreNegosDone;
use App\Notifications\AppelOffreTerminer;
use App\Notifications\CountdownNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class NotificationShow extends Component

{
    use WithFileUploads;
    public $notification;
    public $id;
    public $user;
   
    public $dateTot;
    public $dateTard;
    public $timeStart;
    public $timeEnd;
    public $dayPeriod;

    public $amountDeposit;
    public $roiDeposit;
    public $userDeposit;

    public $userConnected;

    public $operator;

    public $operatorRecu;
    public $phonenumber;
    public $phonenumberRecu;

    public $existingRequest;

    public $id_sos;

    public $receipt;
    public $achatdirect;







    protected $rules = [
        'userSender' => 'required|array',
        'userSender.*' => 'integer|exists:users,id',
        'montantTotal' => 'required|numeric',
        'messageA' => 'required|string',
        'messageR' => 'required|string',
        'notifId' => 'required|uuid|exists:notifications,id',
        'idProd' => 'required|integer|exists:produit_services,id',
    ];

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);

        $this->amountDeposit = $this->notification->data['amount'] ?? null;
        $this->roiDeposit = $this->notification->data['roi'] ?? null;
        $this->userDeposit = User::find($this->notification->data['user_id'] ?? null);

        $userCennecedid = Auth::user()->id;

        $this->userConnected = User::find($userCennecedid);

        $this->id_sos = $this->notification->data['id_sos'] ?? null;

        $this->operatorRecu = $this->notification->data['operator'] ?? null;

        $this->phonenumberRecu = $this->notification->data['phonenumber'] ?? null;

        // Vérifiez si la demande existe déjà dans la table RechargeSos
        $this->existingRequest = RechargeSos::where('id_sos', $this->id_sos)->first();

        $this->psap = $this->notification->data['psap'] ?? null;

        $this->amount = $this->notification->data['amount'] ?? null;

        $this->userId = $this->notification->data['userId'] ?? null;

        $this->demandeur = User::find($this->notification->data['userId'] ?? null);





        $this->resetForm();
    }



    public function resetForm()
    {
        $this->operator = "";
        $this->phonenumber = $this->userConnected->phone;
        $this->receipt = "";
    }



    public function accepteRetrait()
    {
        $userWallet = Wallet::where('user_id', $this->demandeur->id)->first();
        if (!$userWallet) {
            Log::info('Processing userWallet: ' . $userWallet);
            return redirect()->back()->with('error', 'Portefeuille de l\'utilisateur introuvable.');
        }

        $psapWallet = Wallet::where('user_id', $this->psap)->first();
        if (!$psapWallet) {
            Log::info('Processing psapWallet: ' . $psapWallet);
            return redirect()->back()->with('error', 'Portefeuille du PSA introuvable.');
        }

        DB::beginTransaction();

        try {
            $this->notification->update(['reponse' => 'accepter']);

            $userWallet->decrement('balance', $this->amount);
            $psapWallet->increment('balance', $this->amount);

            // $this->createTransaction($this->demandeur->id, Auth::id(), 'Reception', $this->amount);
            // $this->createTransaction($this->demandeur->id,  Auth::id(), 'withdrawal', $this->amount);

            $referenceId = $this->generateIntegerReference();

            $this->createTransactionNew($this->demandeur->id, Auth::id(), 'Envoie', 'COC', $this->amountDeposit, $referenceId, 'Retrait via PSAP');
            $this->createTransactionNew($this->demandeur->id, Auth::id(), 'Réception', 'COC', $this->amountDeposit, $referenceId, 'Retrait via PSAP');

            DB::commit();

            $demandeur = User::find($this->demandeur->id);

            session()->flash('success', 'Le retrait a été accepté.');
            Notification::send($demandeur, new AcceptRetrait($this->notification->id));
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors du retrait.');
        }
    }

    public function refusRetrait()
    {

        $this->notification->update(['reponse' => 'accepter']);

        $demandeur = User::find($this->demandeur->id);

        session()->flash('error', 'Le retrait a été refusé.');
        Notification::send($demandeur, new RefusRetrait($this->notification->id));
    }

    public function acceptDeposit()
    {
        try {
            // Marquer la notification comme acceptée
            Log::info("Début de l'acceptation de la demande avec ID notification : " . $this->notification->id);

            // Validation des données
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
                'operator.string' => 'L’opérateur doit être une chaîne de caractères.',
                'phonenumber.required' => 'Veuillez entrer un numéro de téléphone.',
                'phonenumber.numeric' => 'Le numéro de téléphone doit être un nombre.',
            ]);

            // Vérifier les valeurs après validation
            Log::info("Montant: $this->amountDeposit, ROI: $this->roiDeposit, Opérateur: $this->operator, Téléphone: $this->phonenumber");

            if ($this->existingRequest) {
                // Si la demande existe, afficher un message d'erreur
                session()->flash('error', 'La demande est expirée. Un utilisateur a déjà accepté la demande.');
                return; // Sortir de la méthode
            }

            // Insertion des données dans la table RechargeSos
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
                session()->flash('error', 'Coi non trouvé pour le wallet.');
                return;
            }

            // Décrémenter le solde
            $investCoi->decrement('Solde', $this->amountDeposit);

            $demCfa->increment('Solde', $this->amountDeposit);

            // Générer un ID de référence
            $referenceId = $this->generateIntegerReference();

            // Créer la transaction
            $this->createTransactionNew(Auth::id(), Auth::id(), 'Gele', 'COI', $this->amountDeposit, $referenceId, 'Rechargement SOS');

            $this->createTransactionNew(Auth::id(), $this->notification->data['user_id'], 'Réception', 'CFA', $this->amountDeposit, $referenceId, 'Rechargement SOS');

            $data = [
                'user_id' => Auth::id(),
                'amount' => $this->amountDeposit,
                'roi' => $this->roiDeposit,
                'id_sos' => $this->id_sos,
                'phonenumber' => $this->phonenumber,
                'operator' => $this->operator,

            ];

            Notification::send(User::find($this->notification->data['user_id']), new DepositRecu($data));

            // Message de succès et réinitialisation du formulaire
            session()->flash('success', 'La demande de dépôt a été acceptée et enregistrée avec succès.');
            $this->resetForm();

            $this->notification->update(['reponse' => 'Accepté']);
        } catch (\Exception $e) {
            // Enregistrer l'erreur dans les logs
            Log::error('Erreur lors de l’acceptation de la demande de dépôt : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l’acceptation de la demande de dépôt : ' . $e->getMessage());
        }
    }

    public function rejectDeposit()
    {
        session()->flash('success', 'La demande de dépôt a été refusé et enregistrée avec succès.');
        $this->resetForm();

        $this->notification->update(['reponse' => 'Refusé']);
    }

    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
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
    public function sendRecu()
    {
        $this->validate([
            'receipt' => 'required|image|max:1024', // Limite la taille du fichier à 1MB
        ], [
            'receipt.required' => 'Veuillez sélectionner une photo.',
            'receipt.image' => 'Le fichier doit être une image.',
            'receipt.max' => 'La taille maximale de l\'image est de 1Mo.',
        ]);
        $receiptPath = $this->handlePhotoUpload('receipt');
        Log::info("Image reçue téléchargée et stockée dans le chemin : {$receiptPath}");

        $data = [
            'user_id' => Auth::id(),
            'amount' => $this->amountDeposit,
            'roi' => $this->roiDeposit,
            'receipt' => $receiptPath,
        ];


        $owner = User::find($this->userDeposit);

        Notification::send($owner, new DepositSend($data));

        $this->notification->update(['reponse' => 'Envoyée']);

        $this->resetForm(); // Réinitialiser le formulaire

        session()->flash('success', 'Le reçu a été envoyé avec succès.');
    }

    public function montantRecu()
    {
        $userWallet = Wallet::where('user_id', $this->userDeposit->id)->first();

        $userCfa = Cfa::where('id_wallet', $userWallet->id)->first();

        if (!$userCfa) {
            session()->flash('error', 'Coi non trouvé pour le wallet.');
            return;
        }

        $userCfa->decrement('Solde', $this->amountDeposit);
        $userWallet->increment('balance', $this->amountDeposit);

        $referenceId = $this->generateIntegerReference();

        $this->createTransactionNew($this->userDeposit->id, $this->userDeposit->id, 'Envoie', 'CFA', $this->amountDeposit, $referenceId, 'Rechargement SOS');
        $this->createTransactionNew($this->userDeposit->id, $this->userDeposit->id, 'Réception', 'COC', $this->amountDeposit, $referenceId, 'Rechargement SOS');



        $this->notification->update(['reponse' => 'Recu']);

        session()->flash('success', 'L\'argent à été recu');
    }

    public function nonrecu()
    {
        $userWallet = Wallet::where('user_id', $this->userDeposit->id)->first();

        $userCfa = Cfa::where('id_wallet', $userWallet->id)->first();

        $userCfa->decrement('Solde', $this->amountDeposit);

        $investWallet = Wallet::where('user_id', Auth::id())->first();
        $investCoi = Coi::where('id_wallet', $investWallet->id)->first();

        $investCoi->increment('Solde', $this->amountDeposit);

        $referenceId = $this->generateIntegerReference();

        $this->createTransactionNew($this->userDeposit->id, Auth::id(), 'Envoie', 'CFA', $this->amountDeposit, $referenceId, 'Rechargement SOS');
        $this->createTransactionNew($this->userDeposit->id, Auth::id(), 'Réception', 'COI', $this->amountDeposit, $referenceId, 'Rechargement SOS');



        if (!$userCfa) {
            session()->flash('error', 'Coi non trouvé pour le wallet.');
            return;
        }
        $this->notification->update(['reponse' => 'Non recu']);

        session()->flash('success', 'L\'argent à n\'a pas été recu');
    }


    public function render()
    {
        return view('livewire.notification-show');
    }
}
