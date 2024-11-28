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
    public $montantTotal;
    public $userSender = [];
    public $messageA = "Commande de produit en cours / Préparation a la livraison";
    public $messageR = "Achat refuser / Produits plus disponible";
    public $notifId;
    public $idProd;
    public $idProd2;
    public $userTrader;
    public $code_unique = '';
    public $id_trader;
    public $prixTrade;
    public $secondsRemaining = 60; // 60 seconds = 1 minute
    public $timerInterval;

    public $quantite;
    public $quantiteC;

    public $localite;
    public $specificite;
    public $nameprod;

    public $modalOpen = false;

    public $userFour = null;
    public $code_livr;
    public $countdownStarted = false;
    public $timeRemaining = null;
    public $endTime = null;

    public $nameSender;
    public $id_sender  = [];
    public $idsender;
    public $namefourlivr;
    public $comments = [];
    public $userComment;
    public $commentCount;
    public $oldestCommentDate;
    public $serverTime;
    public $isTempsEcoule;
    public $tempsEcoule;
    public $oldestComment;
    public $nombreLivr;
    public $difference;

    //test
    public $countdownTime;
    public $timerStarted;
    public $timeleft;
    public $produitfat;

    public $totalPrice;

    public $id_livreur;


    public $livreur;

    public $client;

    public $dateLivr;

    public $date_livr;

    public $matine;

    public $matine_client;
    public $prixProd;
    public $produit;
    public $userWallet;
    public $nameProd;
    public $prix;
    public $sommeQuantites;
    public $nombreParticp;
    public $name;
    public $produit_id;
    public $oldestNotificationDate;
    public $prixArticleNegos;
    public $quantitE;
    //achat direct de l'offre
    public $selectedSpec = false;
    public $selectedOption;
    public $options = [
        'Achat avec livraison',
        'Take Away',
        'Reservation',
    ];
    public $photoProd;

    // appel offre grouper
    public $appelOffreGroup;
    public $sumquantite;
    public $datePlusAncienne;
    public $appelOffreGroupcount;
    //ciblage des livreur
    public $clientPays;
    public $clientCommune;
    public $clientContinent;
    public $clientSous_Region;
    public $clientDepartement;
    public $livreurs;
    public $Idsender;
    public $livreursIds;
    public $livreursCount;

    public $psap;

    public $amount;

    public $userId;

    public $demandeur;
    public $locked = false; // Déverrouillé par défaut

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


    public function mount($id)
    {

    }






    public function render()
    {
        return view('livewire.notification-show');
    }
}
