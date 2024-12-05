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




    public function render()
    {
        return view('livewire.notification-show');
    }
}
