<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use Exception;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Comment;
use Livewire\Component;
use App\Models\Countdown;

use App\Models\OffreGroupe;
use App\Models\Transaction;
use Livewire\Attributes\On;
use App\Models\Consommation;
use App\Models\groupagefact;
use App\Rules\ArrayOrInteger;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use Illuminate\Support\Carbon;
use App\Models\NotificationLog;
use App\Notifications\mainleve;
use App\Notifications\AppelOffre;

use App\Notifications\RefusAchat;
use App\Notifications\RefusVerif;
use App\Notifications\acceptAchat;
use App\Notifications\colisaccept;
use App\Notifications\commandVerif;
use App\Notifications\mainlevefour;
use Illuminate\Support\Facades\Log;
use App\Notifications\NegosTerminer;
use Illuminate\Support\Facades\Auth;
use App\Notifications\livraisonVerif;
use App\Notifications\mainleveclient;
use App\Notifications\OffreNegosDone;
use App\Notifications\AppelOffreTerminer;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class NotificationShow extends Component

{
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
    public $comments;
    public $userComment;
    public $commentCount;
    public $oldestCommentDate;
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

    protected $listeners = ['startTimer'];

    public $id_livreur;

    public $code_verif;

    public $livreur;

    public $client;

    public $dateLivr;

    public $date_livr;

    public $matine;

    public $matine_client;


    public $prixProd;






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
        $this->montantTotal = $this->notification->data['montantTotal'] ?? null;
        $this->userSender = $this->notification->data['userSender'] ?? [];
        $this->notifId = $this->notification->id;
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->userTrader = $this->notification->data['userTrader'] ?? null;
        $this->id_trader = Auth::user()->id ?? null;
        $this->user = Auth::user()->id ?? null;
        $this->code_unique = $this->notification->data['code_unique'] ?? null;
        $this->quantite = $this->notification->data['quantité'] ?? null;
        $this->quantiteC = $this->notification->data['quantite'] ?? $this->notification->data['quantity'] ?? null;
        $this->localite = $this->notification->data['localite'] ?? null;
        $this->specificite = $this->notification->data['specificity'] ?? null;
        $this->userFour = User::find($this->notification->data['id_trader'] ?? null);
        $this->code_livr = $this->notification->data['code_livr'] ?? null;

        $this->prixProd = $this->notification->data['prixProd'] ?? null;

        $data = $this->notification->data['userSender'] ?? null;

        if (is_string($data)) {
            $decodedData = json_decode($data, true);
            $this->nameSender = is_array($decodedData) ? $decodedData : explode(',', $decodedData);
        } elseif (is_array($data)) {
            $this->nameSender = $data;
        } else {
            $this->nameSender = null;
        }



        $this->idsender = $this->notification->data['id_sender'] ?? null;

        if (array_key_exists('id_sender', $this->notification->data)) {
            $idSender = $this->notification->data['id_sender'];

            if (is_array($idSender)) {
                //If $idSender is already an array, assign it directly
                $this->id_sender = $idSender;
            } else {
                // If $idSender is a string, use explode to convert it to an array
                $this->id_sender = explode(',', $idSender);
            }
        } else {
            //Handle the case where 'id_sender' does not exist
            $this->id_sender = null; // or any other default value you prefer
        }


        // $data = $this->notification->data['id_sender'] ?? null;
        // $this->id_sender = is_array($data) ? $data : explode(',', $data);


        $this->difference = $this->notification->data['difference'] ?? null;
        $this->nameprod = $this->notification->data['productName'] ?? null;

        $this->date_livr = $this->notification->data['date_livr'] ?? null;



        $this->matine_client = $this->notification->data['matine'] ?? null;
        //pour la facture
        $this->produitfat = ($this->notification->type === 'App\Notifications\AppelOffreGrouperNotification'
            || $this->notification->type === 'App\Notifications\AppelOffreTerminer'
            || $this->notification->type === 'App\Notifications\AppelOffre'
            || $this->notification->type === 'App\Notifications\OffreNotifGroup'
            ||  $this->notification->type === 'App\Notifications\OffreNotif')
            ? null
            : (ProduitService::find($this->notification->data['idProd']) ?? null);

        // $this->produitfat = ProduitService::find($this->notification->data['idProd']) ?? null;
        $this->idProd = $this->notification->data['idProd'] ?? null;

        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);

        $this->id_livreur = $this->notification->data['id_livreur'] ?? null;

        $this->livreur = User::find($this->notification->data['id_livreur'] ?? null);

        $this->client = User::find($this->notification->data['id_client'] ?? null);



        //code unique recuperation dans render
        // Vérifier si 'code_unique' existe dans les données de notification
        $codeUnique = $this->notification->data['code_unique'] ?? $this->notification->data['code_livr'] ?? null;
        $this->comments = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();
        // Récupérer le commentaire de l'utilisateur connecté
        $this->userComment = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->where('id_trader', $this->user)
            ->first();

        // Compter le nombre de commentaires
        $this->commentCount = $this->comments->count();

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Comment::where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('created_at', 'asc')
            ->first();

        // Initialiser la variable pour la date du plus ancien commentaire
        $this->oldestCommentDate = $this->oldestComment ? $this->oldestComment->created_at : null;

        // Ajouter 5 heures à la date la plus ancienne, s'il y en a une
        $this->tempsEcoule = $this->oldestCommentDate ? Carbon::parse($this->oldestCommentDate)->addMinutes(1) : null;

        // Vérifier si $tempsEcoule est écoulé
        $this->isTempsEcoule = $this->tempsEcoule && $this->tempsEcoule->isPast();

        //gestion du temps et de la soumission dans countdown table
        $countdown = Countdown::where('user_id', Auth::id())
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if ($countdown) {
            $this->countdownStarted = true;
        }

        $this->nombreLivr = User::where('actor_type', 'livreur')->count();

        // Recherche dans la table produit_service
        // Recherche dans la table produit_service pour récupérer l'ID du produit
        if (isset($this->notification->data['nameprod']) && isset($this->notification->data['id_trader'])) {
            $produitService = ProduitService::where('name', $this->notification->data['nameprod'])
                ->where('user_id', $this->notification->data['id_trader'])
                ->first();

            if ($produitService) {
                $this->idProd2 = $produitService->id;
            }
        }
    }


    public function valider()
    {
        //prix final
        $this->totalPrice = (int) ($this->notification->data['quantiteC'] * $this->notification->data['prixProd']) + $this->notification->data['prixTrade'];

        // Calculer le prix total
        $montantTotal = $this->totalPrice;

        if (!$this->user) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userSender = User::find($this->user); // Assurez-vous de récupérer l'objet utilisateur

        if (!$userSender) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id',  $userSender->id)->first();

        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        $requiredAmount = $montantTotal;

        if ($userWallet->balance < $requiredAmount) {
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        $userWallet->decrement('balance', $requiredAmount);
        $this->createTransaction($userSender->id, $userSender->id, 'Envoie', $requiredAmount);

        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->code_unique,
            'id_trader' => $this->namefourlivr->user->id,
            'localité' => $this->localite,
            'quantite' => $this->notification->data['quantiteC'],
            'id_livreur' => $this->userFour->id,
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd']
        ];


        Notification::send($userSender, new commandVerif($data));

        $this->notification->update(['reponse' => 'valide']);
        $this->validate();
    }

    public function mainleve()
    {

        $id_client = Auth::user()->id;

        $livreur = User::find($this->id_livreur);



        $fournisseur = User::find($this->namefourlivr->user->id);

        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->code_unique,
            'id_trader' => $this->namefourlivr->user->id,
            'localité' => $this->localite,
            'quantite' => $this->quantiteC,
            'id_client' => $id_client,
            'id_livreur' => $this->id_livreur,
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd']

        ];

        Notification::send($livreur, new mainleve($data));

        Notification::send($fournisseur, new mainlevefour($data));



        $this->notification->update(['reponse' => 'mainleve']);
        $this->validate();
    }

    public function departlivr()
    {
        $id_livreur = Auth::user()->id;

        $this->validate([
            'dateLivr' => 'required|date',
            'matine' => 'required'
        ], [
            'dateLivr.required' => 'La date de livraison est requise.',
            'dateLivr.date' => 'La date de livraison doit être une date valide.',
            'matine.required' => 'La matinée ou soirée est requise.',
        ]);

        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->code_unique,
            'id_trader' => $this->notification->data['id_trader'],
            'localité' => $this->localite,
            'quantite' => $this->quantiteC,
            'id_client' => $this->notification->data['id_client'],
            'id_livreur' => $id_livreur,
            'date_livr' => $this->dateLivr,
            'matine' => $this->matine,
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd']
        ];

        Notification::send($this->client, new mainleveclient($data));

        $this->notification->update(['reponse' => 'mainleveclient']);

        session()->flash('message', 'Livraison marquée comme livrée.');
    }

    public function verifyCode()
    {
        $this->validate([
            'code_verif' => 'required|string|size:10',
        ], [
            'code_verif.required' => 'Le code de vérification est requis.',
            'code_verif.string' => 'Le code de vérification doit être une chaîne de caractères.',
            'code_verif.size' => 'Le code de vérification doit être exactement de 10 caractères.',
        ]);
        if ($this->code_verif === $this->notification->data['code_unique']) {
            session()->flash('succes', 'Code valide.');
        } else {
            session()->flash('error', 'Code invalide.');
        }
    }

    public function acceptColis()
    {

        $livreur = User::find($this->notification->data['id_livreur']);

        $fournisseur = User::find($this->notification->data['id_trader']);

        $client = User::find(Auth::user()->id);

        $produit = ProduitService::find($this->notification->data['idProd']);

        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->code_unique,
            'id_trader' => $this->notification->data['id_trader'],
            'localité' =>  $this->notification->data['localité'],
            'quantite' => $this->notification->data['quantite'],
            'id_client' => $this->notification->data['id_client'],
            'id_livreur' => $this->notification->data['id_livreur'],
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd'],
        ];

        // recuperation de prote-feuille

        $clientWallet = Wallet::where('user_id', Auth::user()->id)->first();

        if (!$clientWallet) {
            session()->flash('error', 'Portefeuille du client introuvable.');
            return;
        }

        $fournisseurWallet = Wallet::where('user_id', $this->notification->data['id_trader'])->first();

        if (!$fournisseurWallet) {
            session()->flash('error', 'Portefeuille du fournisseur introuvable.');
            return;
        }

        $livreurWallet = Wallet::where('user_id', $this->notification->data['id_livreur'])->first();

        if (!$livreurWallet) {
            session()->flash('error', 'Portefeuille du livreur introuvable.');
            return;
        }

        $requiredAmount = $this->notification->data['quantite'] * $this->notification->data['prixProd'];

        $pourcentSomme  = $requiredAmount * 0.1;

        $totalSom = $requiredAmount - $pourcentSomme;

        if ($fournisseur->parrain) {
            $commTraderParrain = $pourcentSomme * 0.05;

            $commTraderParrainWallet = Wallet::where('user_id', $fournisseur->parrain)->first();

            $commTraderParrainWallet->increment('balance', $commTraderParrain);
        }

        if ($client->parrain) {
            $commSenderParrain = $pourcentSomme * 0.05;

            $commSenderParrainWallet = Wallet::where('user_id', $client->parrain)->first();

            $commSenderParrainWallet->increment('balance', $commSenderParrain);
        }

        // debit

        // $clientWallet->decrement('balance', $totalSom);

        $fournisseurWallet->increment('balance', $totalSom);

        $livreurWallet->increment('balance', $this->notification->data['prixTrade']);


        // transactions

        //transation fournisseur

        $this->createTransaction(Auth::user()->id, $this->notification->data['id_trader'], 'Reception', $totalSom);

        //transaction client

        // $this->createTransaction(Auth::user()->id, $this->notification->data['id_trader'], 'Envoie', $totalSom);

        //transtion livreur

        $this->createTransaction(Auth::user()->id, $this->notification->data['id_livreur'], 'Reception', $this->notification->data['prixTrade']);




        Notification::send($client, new colisaccept($data));

        Notification::send($fournisseur, new colisaccept($data));

        Notification::send($livreur, new colisaccept($data));


        $this->notification->update(['reponse' => 'colisaccept']);
        $this->validate();
    }

    public function refuseColis()

    {

        $this->totalPrice = (int) ($this->notification->data['quantite'] * $this->notification->data['prixProd']) + $this->notification->data['prixTrade'];

        $montantTotal = $this->totalPrice;




        $livreur = User::find($this->notification->data['id_livreur']);

        $fournisseur = User::find($this->notification->data['id_trader']);


        $client = User::find($this->notification->data['id_client']);

        $clientWallet = Wallet::where('user_id', $this->notification->data['id_client'])->first();

        if (!$clientWallet) {
            session()->flash('error', 'Portefeuille du client introuvable.');
            return;
        }

        $livreurWallet = Wallet::where('user_id', $this->notification->data['id_livreur'])->first();

        if (!$livreurWallet) {
            session()->flash('error', 'Portefeuille du livreur introuvable.');
            return;
        }

        $clientWallet->increment('balance', $montantTotal);

        $livreurWallet->increment('balance', $this->notification->data['prixTrade']);

        $this->createTransaction($this->notification->data['id_trader'], $this->notification->data['id_client'], 'Reception', $montantTotal);

        $this->createTransaction($this->notification->data['id_client'], $this->notification->data['id_livreur'], 'Reception', $this->notification->data['prixTrade']);

        Notification::send($livreur, new RefusVerif('Le colis à été refuser !'));

        Notification::send($fournisseur, new RefusVerif('Le colis à été refuser !'));

        Notification::send($client, new RefusVerif('Le colis à été refuser !'));




        $this->notification->update(['reponse' => 'refuseVereif']);
        $this->validate();
    }

    public function accepter()
    {


        $this->notification->update(['reponse' => 'accepte']);

        $userId = Auth::id();
        $userWallet = Wallet::where('user_id', $userId)->first();
        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        $notification = NotificationEd::find($this->notification->id);
        $notification->reponse = 'accepte';
        $notification->save();

        // $userSender = User::find($this->notification->data['userSender']);
        $requiredAmount = $this->notification->data['montantTotal'];
        $pourcentSomme  = $requiredAmount * 0.1;
        $totalSom = $requiredAmount - $pourcentSomme;

        $code_livr = isset($this->code_unique) ? $this->code_unique : $this->genererCodeAleatoire(10);

        $produit = Produitservice::find($this->notification->data['idProd'] ?? $this->idProd2);

        // $userTrader = User::find($userId);
        // $this->handleCommission($userTrader, $pourcentSomme, 'Trader');
        // $this->handleCommission($userSender, $pourcentSomme, 'Sender');

        // $userWallet->increment('balance', $totalSom);
        // $this->createTransaction($userSender->id, $userId, 'Reception', $totalSom);
        // $this->createTransaction($userSender->id, $userId, 'Envoie', $requiredAmount);

        // Notification::send($userSender, new acceptAchat($this->messageA));

        $data = [
            'idProd' => $this->notification->data['idProd'] ?? $this->idProd2,
            'id_trader' => $this->userTrader ?? $this->notification->data['id_trader'],
            'totalSom' => $requiredAmount,
            'quantite' => $this->notification->data['quantité'] ?? $this->notification->data['quantiteC'],
            'localite' =>  $this->notification->data['localite'],
            'userSender' =>  $this->notification->data['userSender'] ?? $this->notification->data['id_sender'],
            'code_livr' => $code_livr,
            'prixProd' => $this->notification->data['prixTrade'] ?? $produit->prix

        ];

        $livreurs = User::where('actor_type', 'livreur')->get();

        foreach ($livreurs as $livreur) {
            Notification::send($livreur, new livraisonVerif($data));
        }

        session()->flash('success', 'Achat accepté.');

        $this->modalOpen = false;
    }
    private function genererCodeAleatoire($longueur)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $code = '';

        for ($i = 0; $i < $longueur; $i++) {
            $code .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        return $code;
    }

    public function refuser()
    {
        $this->notification->update(['reponse' => 'refuser']);

        $userId = Auth::id();
        if (!$userId) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $notification = NotificationEd::find($this->notification->id);
        $notification->reponse = 'refuser';
        $notification->save();

        $userSender = User::find($this->notification->data['userSender']);
        $requiredAmount = $this->notification->data['montantTotal'];
        $userWallet = Wallet::where('user_id', $userSender->id)->first();
        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        $userWallet->increment('balance', $requiredAmount);
        $this->createTransaction($userId, $userSender->id, 'Reception', $requiredAmount);

        Notification::send($userSender, new RefusAchat($this->messageR));

        session()->flash('success', 'Achat refusé.');
        // $this->emit('notificationUpdated');
    }

    public function refuseVerif()
    {

        $this->totalPrice = (int) ($this->notification->data['quantite'] * $this->notification->data['prixProd']) + $this->notification->data['prixTrade'];

        $montantTotal = $this->totalPrice;




        $livreur = User::find($this->notification->data['id_livreur']);

        $fournisseur = User::find($this->notification->data['id_trader']);


        $client = User::find($this->notification->data['id_client']);

        $clientWallet = Wallet::where('user_id', $this->notification->data['id_client'])->first();

        if (!$clientWallet) {
            session()->flash('error', 'Portefeuille du client introuvable.');
            return;
        }

        $clientWallet->increment('balance', $montantTotal);

        $this->createTransaction($this->notification->data['id_trader'], $this->notification->data['id_client'], 'Reception', $montantTotal);

        Notification::send($livreur, new RefusVerif('Le colis à été refuser !'));

        Notification::send($fournisseur, new RefusVerif('Le colis à été refuser !'));

        Notification::send($client, new RefusVerif('Le colis à été refuser !'));




        $this->notification->update(['reponse' => 'refuseVereif']);
        $this->validate();
    }

    // protected function handleCommission($user, $amount, $type)
    // {
    //     if ($user->parrain) {
    //         $commission = $amount * 0.05;
    //         $parrainWallet = Wallet::where('user_id', $user->parrain)->first();
    //         $parrainWallet->increment('balance', $commission);
    //         $this->createTransaction($user->id, $user->parrain, 'Commission', $commission);
    //     }
    // }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }

    public function commentFormGroupe()
    {
        // Récupérer l'utilisateur authentifié
        $this->validate([
            'code_unique' => 'required|string',
            'quantiteC' => 'required|numeric',
            'prixTrade' => 'required|numeric',
            'id_sender' => 'required|array',
            'id_sender.*' => 'numeric',
            'id_trader' => 'required|numeric',
            'nameprod' => 'required|string',
            'difference' => 'required|string',
            'localite' => 'required|string',
            'specificite' => 'required|string',

        ]);

        Comment::create([
            'localite' => $this->notification->data['localite'],
            'specificite' => $this->specificite,
            'prixTrade' => $this->prixTrade,
            'id_sender' => json_encode($this->id_sender),
            'nameprod' => $this->nameprod,
            'code_unique' => $this->code_unique,
            'id_trader' => $this->id_trader,
            'quantiteC' => $this->quantiteC,
        ]);

        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $this->code_unique)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => $this->id_trader,
                // 'userSender' => json_encode($this->id_sender),
                'start_time' => now(),
                'code_unique' => $this->code_unique,
                'difference' => $this->difference,
            ]);

            $this->countdownStarted = true;
        }

        session()->flash('success', 'Commentaire créé avec succès!');

        $this->reset(['prixTrade']);
    }
    public function commentForm()
    {
        // Récupérer l'utilisateur authentifié
        $this->validate([
            'code_unique' => 'required|string',
            'quantiteC' => 'required|numeric',
            'prixTrade' => 'required|numeric',
            'idsender' => 'required|numeric',
            'id_trader' => 'required|numeric',
            'nameprod' => 'required|string',
            'difference' => 'required|string',
            'localite' => 'required|string',
            'specificite' => 'required|string',

        ]);

        Comment::create([
            'localite' => $this->notification->data['localite'],
            'specificite' => $this->specificite,
            'prixTrade' => $this->prixTrade,
            'id_sender' => json_encode($this->idsender),
            'nameprod' => $this->nameprod,
            'code_unique' => $this->code_unique,
            'id_trader' => $this->id_trader,
            'quantiteC' => $this->quantiteC,
        ]);


        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $this->code_unique)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => $this->id_trader,
                'userSender' => $this->idsender,
                'start_time' => now(),
                'code_unique' => $this->code_unique,
                'difference' => $this->difference,
            ]);
        }

        session()->flash('success', 'Commentaire créé avec succès!');

        $this->reset(['prixTrade']);
    }
    public function commentFormLivr()
    {

        // Valider les données
        $validatedData = $this->validate([
            'id_trader' => 'required|numeric',
            'code_livr' => 'required|string',
            'userSender' => 'required|numeric',
            'prixTrade' => 'required|numeric',
            'quantiteC' => 'required|numeric',
            'idProd' => 'required|numeric',
            'prixProd' => 'required|numeric'
        ]);


        // Créer un commentaire
        Comment::create([
            'prixTrade' => $validatedData['prixTrade'],
            'code_unique' => $validatedData['code_livr'],
            'id_trader' => $validatedData['id_trader'],
            'quantiteC' => $validatedData['quantiteC'],
            'id_prod' => $validatedData['idProd'],
            'prixProd' => $validatedData['prixProd'],
        ]);


        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $validatedData['code_livr'])
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $this->userSender,
                'start_time' => now(),
                'code_unique' => $validatedData['code_livr'],
            ]);
        }


        // Afficher un message de succès
        session()->flash('success', 'Commentaire créé avec succès!');

        // Réinitialiser le champ du formulaire
        $this->reset(['prixTrade']);

        // Émettre un événement pour notifier les autres utilisateurs
        $this->dispatch('priceSubmitted', $validatedData);
    }
    public function commentFormLivrGroup()
    {
        // Valider les données
        $validatedData = $this->validate([
            'id_trader' => 'required|numeric',
            'code_livr' => 'required|string',
            'nameSender' => 'required|array',
            'nameSender.*' => 'numeric',
            'prixTrade' => 'required|numeric',
            'quantiteC' => 'required|numeric',
            'idProd' => 'required|numeric',
        ]);


        // Créer un commentaire
        Comment::create([
            'prixTrade' => $validatedData['prixTrade'],
            'code_unique' => $validatedData['code_livr'],
            'id_trader' => $validatedData['id_trader'],
            'quantiteC' => $validatedData['quantiteC'],
            'id_prod' => $validatedData['idProd'],
        ]);

        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $validatedData['code_livr'])
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            groupagefact::create([
                'usersenders' => json_encode($this->userSender),
                'start_time' => now(),
                'code_unique' => $validatedData['code_livr'],
            ]);
        }


        // Afficher un message de succès
        session()->flash('success', 'Commentaire créé avec succès!');

        // Réinitialiser le champ du formulaire
        $this->reset(['prixTrade']);
    }


    public function render()
    {

        return view('livewire.notification-show');
    }
}
