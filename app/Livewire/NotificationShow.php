<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Models\AchatDirect;
use App\Models\Admin;
use App\Models\AppelOffreGrouper;
use App\Models\userquantites;
use App\Notifications\AllerChercher;
use App\Notifications\attenteclient;
use App\Notifications\VerifUser;
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
use App\Models\Livraisons;
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
use App\Notifications\AchatBiicf;
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
use App\Notifications\CountdownNotification;
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
    public $comments = [];
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

    public $id_livreur;

    public $code_verif;

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
        $this->code_unique = $this->notification->data['code_unique'] ?? $this->notification->data['Uniquecode'] ?? null;
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
            || $this->notification->type === 'App\Notifications\NegosTerminer'
            || $this->notification->type === 'App\Notifications\OffreNegosNotif'
            || $this->notification->type === 'App\Notifications\OffreNegosDone'
            || $this->notification->type === 'App\Notifications\AOGrouper'
            ||  $this->notification->type === 'App\Notifications\OffreNotif')
            ? null
            : (ProduitService::find($this->notification->data['idProd']) ?? $this->notification->data['produit_id'] ?? null);

        // $this->produitfat = ProduitService::find($this->notification->data['idProd']) ?? null;
        $this->idProd = $this->notification->data['idProd'] ?? null;

        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);

        $this->id_livreur = $this->notification->data['id_livreur'] ?? null;

        $this->livreur = User::find($this->notification->data['id_livreur'] ?? null);

        $this->client = User::find($this->notification->data['id_client'] ?? null);
        //achat direct dans notif show
        // $this->nameProd = $this->produit->name;
        // $this->userTrader = $this->produit->user->id;
        // $this->idProd = $this->produit->id;

        $this->userWallet = Wallet::where('user_id', $this->user)->first();
        // $this->prixArticleNegos = $this->notification->data['quantite'] * $this->namefourlivr->prix;


        //code unique recuperation dans render
        // Vérifier si 'code_unique' existe dans les données de notification
        $codeUnique = $this->notification->data['code_unique']
            ?? $this->notification->data['code_livr']
            ?? $this->notification->data['Uniquecode'] ?? null;

        //offre negocier grouper
        $this->name = $this->notification->data['produit_name'] ?? null;
        $this->produit_id = $this->notification->data['produit_id'] ?? null;
        $notificationsNegos = DatabaseNotification::where('type', 'App\Notifications\OffreNegosNotif')
            ->where(function ($query) use ($codeUnique) {
                $query->where('data->code_unique', $codeUnique);
            })
            ->get();
        $this->oldestNotificationDate = $notificationsNegos->min('created_at');
        $this->sommeQuantites = OffreGroupe::where('code_unique', $codeUnique)
            ->sum('quantite');
        $this->nombreParticp = OffreGroupe::where('code_unique', $codeUnique)
            ->distinct('user_id')
            ->count();

        $comments = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();
        foreach ($comments as $comment) {
            $this->commentsend($comment);
        }
        // Récupérer le commentaire de l'utilisateur connecté
        $this->userComment = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->where('id_trader', $this->user)
            ->first();

        // Compter le nombre de commentaires
        $this->commentCount = $comments->count();

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




        // Recherche dans la table produit_service pour récupérer l'ID du produit
        if (isset($this->notification->data['nameprod']) && isset($this->notification->data['id_trader'])) {
            $produitService = ProduitService::where('name', $this->notification->data['nameprod'])
                ->where('user_id', $this->notification->data['id_trader'])
                ->first();

            if ($produitService) {
                $this->idProd2 = $produitService->id;
            }
        }


        // Vérification avant de récupérer le produit
        if (isset($this->notification->data['idProd']) || isset($this->notification->data['produit_id'])) {
            $produitId = $this->notification->data['idProd'] ?? $this->notification->data['produit_id'] ?? null;
            $this->produit = ProduitService::find($produitId);

            if ($this->produit) {
                $this->nameProd = $this->produit->name;
                $this->userTrader = $this->produit->user->id;
                $this->idProd = $this->produit->id;
                $this->prix = $this->produit->prix;
            } else {
                $this->nameProd = $this->userTrader = $this->idProd = $this->prix = null;
            }
        } else {
            $this->produit = null;
            $this->nameProd = $this->userTrader = $this->idProd = $this->prix = null;
        }

        $Idoffre = $this->notification->data['offre_id'] ?? null;

        // Attempt to retrieve the grouped offer by its ID
        $this->appelOffreGroup = AppelOffreGrouper::find($Idoffre);

        // Check if $appelOffreGroup is null before proceeding
        if ($this->appelOffreGroup) {
            $codesUniques = $this->appelOffreGroup->codeunique;

            // Retrieve the oldest date for the unique code
            $this->datePlusAncienne = AppelOffreGrouper::where('codeunique', $codesUniques)->min('created_at');

            // Sum the quantities for the unique code
            $this->sumquantite = AppelOffreGrouper::where('codeunique', $codesUniques)->sum('quantity');

            // Count the number of grouped offers
            $this->appelOffreGroupcount = AppelOffreGrouper::where('codeunique', $codesUniques)->count();
        } else {
            // Handle the case where no offer was found
            $this->datePlusAncienne = null;
            $this->sumquantite = 0;
            $this->appelOffreGroupcount = 0;
        }

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();

        // $this->loadDetails();
    }

    // public $notificationId;
    // public $clientPays;
    // public $clientVille;
    // public $livreurs;
    // public $Idsender;



    // public function loadDetails()
    // {
    //     $this->Idsender = $this->notification->data['userSender'];

    //     $client = User::findOrFail($this->Idsender);
    //     $this->clientPays = $client->country;
    //     $this->clientVille = $client->address;

    //     $this->livreurs = Livraisons::where('pays', $this->clientPays)
    //         ->where('ville', $this->clientVille)
    //         ->get();
    // }

    public function storeoffre()
    {
        try {
            // Valider les données du formulaire
            Log::info('Début de la validation des données du formulaire.');
            $validatedData = $this->validate([
                'quantite' => 'required|integer',
                'localite' => 'required|string',
                'selectedOption' => 'required|string',
            ]);
            Log::info('Données du formulaire validées.', ['validated_data' => $validatedData]);

            // Créer un nouvel enregistrement dans la table offregroupe
            Log::info('Création d\'un nouvel enregistrement dans AppelOffreGrouper.');
            $offregroupe = new AppelOffreGrouper();
            $offregroupe->codeunique = $this->appelOffreGroup->codeunique;
            $offregroupe->user_id = Auth::id();
            $offregroupe->quantity = $validatedData['quantite'];
            $offregroupe->save();
            Log::info('Enregistrement dans AppelOffreGrouper sauvegardé.', ['offregroupe_id' => $offregroupe->id]);

            // Ajouter dans la table userquantites
            Log::info('Ajout d\'une nouvelle quantité dans userquantites.');
            $quantite = new userquantites();
            $quantite->code_unique = $this->appelOffreGroup->codeunique; // Vous devez définir `codeUnique` correctement
            $quantite->user_id = Auth::id(); // Vous devez définir `userId` correctement
            $quantite->localite = $validatedData['localite']; // Vous devez définir `userId` correctement
            $quantite->quantite = $validatedData['quantite'];
            $quantite->type_achat = $validatedData['selectedOption'];
            $quantite->save();

            Log::info('Enregistrement dans userquantites sauvegardé.', ['quantite_id' => $quantite->id]);
            $this->reset('quantite', 'localite', 'selectedOption');

            // Flash success message
            session()->flash('success', 'Quantité ajoutée avec succès');
            Log::info('Message de succès flashé.', ['message' => 'Quantité ajoutée avec succès']);
        } catch (Exception $e) {
            // Log l'erreur
            Log::error('Erreur lors de l\'enregistrement des données.', ['error' => $e->getMessage()]);
            // Vous pouvez également définir un message d'erreur pour la session si nécessaire
            session()->flash('error', 'Erreur lors de l\'ajout de la quantité.');
        }
    }

    public function takeaway()
    {
        // Log pour vérifier que la méthode est appelée
        Log::info('Méthode takeaway appelée.', ['notification' => $this->notification]);

        // Extraire les données correctement
        $notificationData = $this->notification->data;


        // Récupérer le produit par son ID
        $produit = ProduitService::find($notificationData['idProd']);

        // Vérifier si le produit existe
        if ($produit) {
            $prixProd = $produit->prix;
        } else {
            Log::error('Produit non trouvé.', ['idProd' => $notificationData['idProd']]);
            session()->flash('error', 'Produit non trouvé.');
            return;
        }

        $code_livr = isset($this->code_unique) ? $this->code_unique : $this->genererCodeAleatoire(10);


        $details = [
            'code_unique' => $code_livr ?? null,
            'id_trader' => $notificationData['userTrader'] ?? null, // Correction: Utiliser 'userTrader'
            'idProd' => $notificationData['idProd'] ?? null,
            'quantiteC' => $notificationData['quantité'] ?? null, // Correction: Utiliser 'quantité'
            'prixProd' => $prixProd ?? null,
        ];

        // Log pour vérifier les détails avant l'envoi de la notification
        Log::info('Détails de la notification préparés.', ['details' => $details]);

        // Assurez-vous que 'userSender' est un utilisateur valide et que CountdownNotification est bien défini
        if (isset($notificationData['userSender'])) {
            $userSender = User::find($notificationData['userSender']);
            if ($userSender) {
                Log::info('Utilisateur expéditeur trouvé.', ['userSenderId' => $userSender->id]);

                // Envoi de la notification
                Notification::send($userSender, new CountdownNotification($details));
                Log::info('Notification envoyée avec succès.', ['userSenderId' => $userSender->id, 'details' => $details]);
            } else {
                Log::error('Utilisateur expéditeur non trouvé.', ['userSenderId' => $notificationData['userSender']]);
                session()->flash('error', 'Utilisateur expéditeur non trouvé.');
            }

            // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
            $notification = $userSender->notifications()->where('type', CountdownNotification::class)->latest()->first();

            if ($notification) {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => 'reserv/take']);
            }
        } else {
            Log::error('Détails de notification non valides.', ['notification' => $this->notification]);
            session()->flash('error', 'Détails de notification non valides.');
        }
        $this->notification->update(['reponse' => 'accepte']);
    }

    public function acceptoffre()
    {

        // Retrieve the currently authenticated user
        $userId = Auth::guard('web')->id();

        // Assurez-vous que la variable $notification est définie et accessible
        $produit = ProduitService::find($this->notification->data['produit_id']);

        // Assurez-vous que $this->notification->data['quantite'] et $this->namefourlivr->prix sont définis et accessibles
        //$quantite = $this->notification->data['quantite'] ?? 0;




        // Retrieve the user's wallet
        $userWallet = Wallet::where('user_id', $userId)->first();
        if (!$userWallet) {
            Log::info('Processing userWallet: ' . $userWallet);
            return redirect()->back()->with('error', 'Portefeuille de l\'utilisateur introuvable.');
        }

        $distinctUserIds = OffreGroupe::where('code_unique', $this->code_unique)
            ->distinct()
            ->pluck('user_id');
        Log::info('Processing userWallet: ' . $distinctUserIds);




        // Retrieve the trader's user model
        // $userTrader = User::find($produit->user_id);
        // if (!$userTrader) {
        //     return redirect()->back()->with('error', 'Utilisateur du trader introuvable.');
        // }


        // Vérifiez si le code_unique existe dans userquantites
        // Vérifiez si le code_unique existe dans userquantites
        // Vérifiez si le code_unique existe dans userquantites
        $userQuantities = userquantites::where('code_unique', $this->code_unique)->get();

        // Log l'état initial de la récupération des données
        Log::info('Recherche du code_unique', ['code_unique' => $this->code_unique, 'count' => $userQuantities->count()]);

        if ($userQuantities->isNotEmpty()) {
            // Groupez par user_id et calculez les quantités totales
            $groupedUserQuantities = $userQuantities->groupBy('user_id')->map(function ($items) {
                return $items->sum('quantite');
            });

            // Log le résultat du regroupement et de la somme
            Log::info('Quantités groupées par utilisateur', ['groupedUserQuantities' => $groupedUserQuantities]);

            // Traitez chaque utilisateur et envoyez la notification
            foreach ($groupedUserQuantities as $userId => $totalQuantite) {
                $userTrader = User::find($userId);  // Trouve l'utilisateur correspondant à l'ID

                if ($userTrader) {

                    $prixUnitaire = $produit->prix ?? 0;

                    // Calcul du prix total de la négociation
                    $prixArticleNego = $totalQuantite * $prixUnitaire;

                    $data = [
                        'nameProd' => $this->nameProd,
                        'quantité' => $totalQuantite,
                        'montantTotal' => $prixArticleNego,
                        'localite' => User::findOrFail(Auth::id())->address,
                        'specificite' => null,
                        'userTrader' => $userId,
                        'userSender' => Auth::id(),
                        'photoProd' => $this->produit->photoProd1,
                        'idProd' => $this->produit_id,
                        'code_unique' => $this->code_unique,
                    ];

                    foreach ($distinctUserIds as $id_trader) {
                        if (is_null($prixArticleNego) || is_null($id_trader) || is_null($this->notifId)) {
                            return redirect()->back()->with('error', 'Données manquantes dans la requête.');
                        }

                        $traderWallet = Wallet::where('user_id', $id_trader)->first();
                        if (!$traderWallet) {
                            return redirect()->back()->with('error', 'Portefeuille du trader introuvable.');
                        }

                        // Update trader's wallet
                        $traderWallet->increment('balance', $prixArticleNego);

                        // Create transaction record for the trader
                        $this->createTransaction($userId, $id_trader, 'Reception', $prixArticleNego);
                    }


                    // Update the user's wallet
                    $userWallet->decrement('balance', $prixArticleNego);
                    // Create transaction record for the user
                    $this->createTransaction($userId, $id_trader, 'Envoie', $prixArticleNego);


                    // Envoyez la notification au client directement
                    Notification::send($userTrader, new AchatBiicf($data));
                    // Log l'envoi de la notification
                    Log::info('Notification envoyée au client', ['user_id' => $userTrader->id, 'quantité' => $totalQuantite]);
                } else {
                    // Log un avertissement si aucun utilisateur n'est trouvé
                    Log::warning('Utilisateur non trouvé pour l\'ID', ['user_id' => $userId]);
                }
            }
        } else {
            // Log si aucune donnée n'est trouvée
            Log::info('Aucune donnée trouvée pour le code_unique', ['code_unique' => $this->code_unique]);
        }





        // Notification::send($userTrader, new AchatBiicf($data));


        //  Notification::send($userTrader, new AchatBiicf($data));
        $this->notification->update(['reponse' => 'accepte']);
    }
    public function refusoffre()
    {
        $this->notification->update(['reponse' => 'refus']);
    }
    public function AchatDirectForm()
    {

        $validated = $this->validate([
            'quantite' => 'required|integer',
            'selectedOption' => 'required|string',
            'localite' => 'required|string|max:255',
            'nameProd' => 'required|string',
            'userTrader' => 'required|exists:users,id',
            'idProd' => 'required|exists:produit_services,id',
            'prixProd' => 'required|numeric',
        ]);

        // dd($validated);

        Log::info('Validation réussie.', $validated);

        $userId = Auth::id();
        $montantTotal = $validated['quantite'] * $validated['prixProd'];

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
            $selectedSpec = $this->selectedSpec; // Assurez-vous que `selectedSpec` est bien défini et récupère la spécification sélectionnée
            $specificites = !empty($selectedSpec) ? $selectedSpec : null;


            // Vérifiez que le tableau $selectedSpecificites n'est pas vide avant de l'utiliser
            // $specificites = !empty($selectedSpecificites) ? implode(', ', $selectedSpecificites) : null;
            Log::info('Selected Specification:', ['selectedSpec' => $this->selectedSpec]);


            $idProd = ProduitService::find($validated['idProd']);
            $photo = $idProd->photoProd1;

            $achat = AchatDirect::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantite'],
                'montantTotal' => $montantTotal,
                'localite' => $validated['localite'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $userId,
                'specificite' => $specificites,
                'photoProd' => $photo,
                'idProd' => $validated['idProd'],
            ]);

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
            // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
            $notification = $owner->notifications()->where('type', AchatBiicf::class)->latest()->first();

            if ($notification) {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => $selectedOption]);
            }

            // $user = User::find($userId);
            $this->reset(['quantite', 'localite', 'selectedSpec', 'selectedOption']);
            session()->flash('success', 'Achat passé avec succès.');
            // $this->dispatch('sendNotification', $user);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    public function add()
    {
        $this->validate([
            'quantitE' => 'required|integer',
            'name' => 'required|string|max:255',
            'produit_id' => 'required|numeric',
            'code_unique' => 'required|string',
        ]);
        // Récupérer l'identifiant de l'utilisateur connecté
        $user_id = Auth::guard('web')->id();

        $offreGroupeExistante = OffreGroupe::where('code_unique', $this->code_unique)->first();

        $differance = $offreGroupeExistante->differance;


        // Créer une nouvelle instance de OffreGroupe
        OffreGroupe::create([
            'name' => $this->name,
            'quantite' =>  $this->quantitE,
            'code_unique' =>  $this->code_unique,
            'produit_id' =>  $this->produit_id,
            'user_id' =>  $user_id,
            'differance' => $differance ?? null,
        ]);
        // Créer une nouvelle instance de OffreGroupe
        userquantites::create([
            'quantite' =>  $this->quantitE,
            'code_unique' =>  $this->code_unique,
            'user_id' =>  $user_id,
        ]);

        session()->flash('success', 'Offre ajoutée avec succès.');
        $this->reset(['quantitE']);
        // Trigger JavaScript event
        $this->dispatch('form-submitted');
    }
    public function valider()
    {
        try {
            // Déterminer le prix unitaire
            $prixUnitaire = $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'];
            Log::info('Prix unitaire déterminé', ['prixUnitaire' => $prixUnitaire]);

            $quantite = $this->notification->data['quantite'] ?? $this->notification->data['quantiteC'];

            // Calculer le prix total
            $this->totalPrice = (int) ($quantite * $prixUnitaire + ($this->notification->data['prixTrade'] ?? 0));
            Log::info('Prix total calculé', ['totalPrice' => $this->totalPrice]);

            // Vérifier si l'utilisateur est authentifié
            if (!$this->user) {
                Log::error('Utilisateur non authentifié.');
                session()->flash('error', 'Utilisateur non authentifié.');
                return;
            }

            $userSender = User::find($this->user);

            if (!$userSender) {
                Log::error('Utilisateur non trouvé avec ID', ['userId' => $this->user]);
                session()->flash('error', 'Utilisateur non authentifié.');
                return;
            }
            Log::info('Utilisateur trouvé', ['userSender' => $userSender]);

            $userWallet = Wallet::where('user_id', $userSender->id)->first();

            if (!$userWallet) {
                Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $userSender->id]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);

            $requiredAmount = $this->totalPrice;

            if ($userWallet->balance < $requiredAmount) {
                Log::error('Fonds insuffisants pour l\'achat', ['balance' => $userWallet->balance, 'requiredAmount' => $requiredAmount]);
                session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
                return;
            }

            // Déduire le montant requis du portefeuille de l'utilisateur
            $userWallet->decrement('balance', $requiredAmount);
            Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

            $this->createTransaction($userSender->id, $userSender->id, 'Envoie', $requiredAmount);

            // Vérifiez si $this->userFour est défini
            if (!isset($this->userFour) || !$this->userFour) {
                Log::error('Livreur introuvable.');
                session()->flash('error', 'Livreur introuvable.');
                return;
            }
            Log::info('Vérification de userFour', ['userFour' => $this->userFour]);

            // Préparer les données de notification
            $data = [
                'idProd' => $this->notification->data['idProd'],
                'code_unique' => $this->code_unique ?? $this->notification->data['code_livr'],
                'id_trader' => $this->namefourlivr->user->id,
                'localité' => $this->localite,
                'quantite' => $quantite,
                'id_livreur' => $this->userFour->id, // Assurez-vous que $this->userFour est défini
                'prixTrade' => $this->notification->data['prixTrade'] ?? null,
                'prixProd' => $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade']
            ];

            $user = [
                'idProd' => $this->notification->data['idProd'],
                'code_unique' => $this->code_unique ?? $this->notification->data['code_livr'],
                'id_trader' => $this->userFour->id,
                'localité' => $this->localite,
                'quantite' => $quantite,
                'id_client' => Auth::id(),
                'prixProd' => $this->notification->data['prixProd']
            ];

            $id_trader = $this->namefourlivr->user->id;
            $traderUser = User::find($id_trader);

            Log::info('Notification envoyée au userSender', ['userId' => $userSender->id, 'data' => $data]);

            if ($this->notification->type_achat == 'reserv/take') {
                Notification::send($userSender, new commandVerif($data));

                $notification = $userSender->notifications()->where('type', commandVerif::class)->latest()->first();

                if ($notification) {
                    $notification->update(['type_achat' => 'reserv/take']);
                }

                // Utilisez && pour vérifier que les deux conditions sont vraies
                Notification::send($traderUser, new VerifUser($user));
            } elseif (!empty($this->notification->data['code_livr']) && !empty($this->notification->data['prixProd'])) {
                // Utilisez && pour vérifier que les deux conditions sont vraies
                Notification::send($traderUser, new VerifUser($user));

                $userWallet = Wallet::where('user_id', $traderUser->id)->first();

                if (!$userWallet) {
                    Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $userSender->id]);
                    session()->flash('error', 'Portefeuille introuvable.');
                    return;
                }
                Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);
                // Déduire le montant requis du portefeuille de l'utilisateur
                $userWallet->decrement('balance', $requiredAmount);
                Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

                $this->createTransaction($userSender->id, $traderUser->id, 'Reception', $requiredAmount);
            } else {
                Notification::send($userSender, new commandVerif($data));

                // Utilisez && pour vérifier que les deux conditions sont vraies
                Notification::send($traderUser, new VerifUser($user));
            }

            // Mettre à jour la notification et valider
            $this->notification->update(['reponse' => 'valide']);
            Log::info('Notification mise à jour', ['notificationId' => $this->notification->id]);

            session()->flash('success', 'Validation effectuée avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur dans la méthode valider', ['message' => $e->getMessage()]);
            session()->flash('error', 'Une erreur est survenue lors de la validation.');
        }
    }
    public function mainleve()
    {
        $id_client = Auth::user()->id;
        Log::info('le id du client', ['id_client' => $id_client]);
        $user = User::find($id_client);

        $livreur = User::find($this->id_livreur);
        Log::info('le id du livreur', ['livreur' => $livreur]);

        $fournisseur = User::find($this->namefourlivr->user->id);
        Log::info('le id du fournisseur', ['fournisseur' => $fournisseur]);

        // Déterminer le prix unitaire
        $prixUnitaire = $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'];
        Log::info('Prix unitaire déterminé', ['prixUnitaire' => $prixUnitaire]);

        $quantite = $this->notification->data['quantite'] ?? $this->notification->data['quantiteC'];

        // Calculer le prix total
        $this->totalPrice = (int) ($quantite * $prixUnitaire + ($this->notification->data['prixTrade'] ?? 0));
        Log::info('Prix total calculé', ['totalPrice' => $this->totalPrice]);

        // Vérifier si l'utilisateur est authentifié
        if (!$fournisseur) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }


        $userWallet = Wallet::where('user_id', $fournisseur->id)->first();


        // Vérifier si le code unique existe dans userquantites
        $userQuantitiesExist = userquantites::where('code_unique', $this->code_unique)->exists();
        Log::info('Vérification de l\'existence du code unique', ['code_unique' => $this->code_unique, 'exists' => $userQuantitiesExist]);

        if ($userQuantitiesExist) {
            // Si le code unique existe, envoyer des notifications aux utilisateurs associés
            $userQuantities = userquantites::where('code_unique', $this->code_unique)->get();
            Log::info('Quantités utilisateur récupérées', ['userQuantities' => $userQuantities]);

            // Groupement par utilisateur et calcul de la somme des quantités
            $userQuantitiesSum = $userQuantities->groupBy('user_id')->map(function ($group) {
                return $group->sum('quantite');
            });
            Log::info('Somme des quantités groupées par utilisateur', ['userQuantitiesSum' => $userQuantitiesSum]);

            foreach ($userQuantitiesSum as $userId => $totalQuantity) {
                $owner = User::find($userId);
                if ($owner) {
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
                    $donne = [
                        'idProd' => $this->notification->data['idProd'],
                        'code_unique' => $this->code_unique,
                        'id_trader' => $this->namefourlivr->user->id,
                        'localité' => $this->localite,
                        'quantite' => $totalQuantity,
                        'id_client' => $id_client,
                        'id_livreur' => $this->id_livreur,
                        'prixTrade' => $this->notification->data['prixTrade'],
                        'prixProd' => $this->notification->data['prixProd']

                    ];
                    Log::info('Notification envoyée', ['ownerId' => $userId, 'data' => $data]);



                    Notification::send($owner, new mainlevefour($donne));
                    Notification::send($livreur, new mainleve($data));
                    Notification::send($fournisseur, new mainlevefour($data));
                }
            }
        } else {

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

            if ($this->notification->type_achat == 'reserv/take') {
                Notification::send($fournisseur, new colisaccept($data));

                $userWallet = Wallet::where('user_id', $fournisseur->id)->first();

                if (!$userWallet) {
                    Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $fournisseur->id]);
                    session()->flash('error', 'Portefeuille introuvable.');
                    return;
                }
                Log::info('Portefeuille trouvé', ['userWallet' => $userWallet]);
                // Déduire le montant requis du portefeuille de l'utilisateur
                $userWallet->decrement('balance', $this->totalPrice);
                Log::info('Solde du portefeuille après déduction', ['newBalance' => $userWallet->balance]);

                $this->createTransaction($user->id, $fournisseur->id, 'Reception', $this->totalPrice);

                Notification::send($user, new colisaccept($data));
            } else {
                Notification::send($livreur, new mainleve($data));

                Notification::send($fournisseur, new mainlevefour($data));
            }
        }


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
        Log::info('Début de la fonction acceptColis', ['notification_id' => $this->notification->id]);

        $livreur = User::find($this->notification->data['id_livreur']);
        $fournisseur = User::find($this->notification->data['id_trader']);
        $client = User::find(Auth::user()->id);
        $produit = ProduitService::find($this->notification->data['idProd']);

        Log::info('Utilisateurs et produit récupérés', [
            'livreur_id' => $livreur->id,
            'fournisseur_id' => $fournisseur->id,
            'client_id' => $client->id,
            'produit_id' => $produit->id
        ]);

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

        Log::info('Données préparées', ['data' => $data]);

        // Récupération des portefeuilles
        $clientWallet = Wallet::where('user_id', Auth::user()->id)->first();
        if (!$clientWallet) {
            Log::error('Portefeuille du client introuvable', ['user_id' => Auth::user()->id]);
            session()->flash('error', 'Portefeuille du client introuvable.');
            return;
        }

        $fournisseurWallet = Wallet::where('user_id', $this->notification->data['id_trader'])->first();
        if (!$fournisseurWallet) {
            Log::error('Portefeuille du fournisseur introuvable', ['user_id' => $this->notification->data['id_trader']]);
            session()->flash('error', 'Portefeuille du fournisseur introuvable.');
            return;
        }

        $livreurWallet = Wallet::where('user_id', $this->notification->data['id_livreur'])->first();
        if (!$livreurWallet) {
            Log::error('Portefeuille du livreur introuvable', ['user_id' => $this->notification->data['id_livreur']]);
            session()->flash('error', 'Portefeuille du livreur introuvable.');
            return;
        }

        $requiredAmount = $this->notification->data['quantite'] * $this->notification->data['prixProd'];
        Log::info('Montant requis calculé', ['requiredAmount' => $requiredAmount]);

        $pourcentSomme  = $requiredAmount * 0.1;
        $totalSom = $requiredAmount - $pourcentSomme;

        Log::info('Pourcentage et montant total calculés', ['pourcentSomme' => $pourcentSomme, 'totalSom' => $totalSom]);

        if ($fournisseur->parrain) {
            $commTraderParrain = $pourcentSomme * 0.05;
            $commTraderParrainWallet = Wallet::where('user_id', $fournisseur->parrain)->first();
            $commTraderParrainWallet->increment('balance', $commTraderParrain);
            Log::info('Commission parrain fournisseur ajouté', ['parrain_id' => $fournisseur->parrain, 'commission' => $commTraderParrain]);
        }

        if ($client->parrain) {
            $commSenderParrain = $pourcentSomme * 0.05;
            $commSenderParrainWallet = Wallet::where('user_id', $client->parrain)->first();
            $commSenderParrainWallet->increment('balance', $commSenderParrain);
            Log::info('Commission parrain client ajouté', ['parrain_id' => $client->parrain, 'commission' => $commSenderParrain]);
        }

        // Débit
        $fournisseurWallet->increment('balance', $totalSom);
        Log::info('Solde du portefeuille du fournisseur mis à jour', ['fournisseur_id' => $fournisseur->id, 'totalSom' => $totalSom]);

        // Transactions
        $this->createTransaction(Auth::user()->id, $this->notification->data['id_trader'], 'Reception', $totalSom);
        Log::info('Transaction fournisseur créée', ['fournisseur_id' => $fournisseur->id, 'totalSom' => $totalSom]);

        // Montant total de la transaction
        $prixTotal = $this->notification->data['prixTrade'];
        $montantAdmin = $prixTotal * 0.10;
        $montantLivreur = $prixTotal - $montantAdmin;

        $livreurWallet->increment('balance', $montantLivreur);
        Log::info('Solde du portefeuille du livreur mis à jour', ['livreur_id' => $livreur->id, 'montantLivreur' => $montantLivreur]);

        $this->createTransaction(Auth::user()->id, $this->notification->data['id_livreur'], 'Reception', $montantLivreur);
        Log::info('Transaction livreur créée', ['livreur_id' => $livreur->id, 'montantLivreur' => $montantLivreur]);

        // Administrateur
        try {
            $admin = Admin::find(1);

            if (!$admin) {
                Log::error('Administrateur introuvable', ['admin_id' => 1]);
                return;
            }

            $adminWallet = Wallet::where('user_id', $admin->user_id)->first();

            if (!$adminWallet) {
                Log::error('Portefeuille de l\'administrateur introuvable', ['admin_id' => $admin->user_id]);
                return;
            }

            // Incrémenter le solde du portefeuille de l'administrateur avec le montant calculé
            $adminWallet->increment('balance', $montantAdmin);

            Log::info('Solde du portefeuille de l\'administrateur mis à jour', ['admin_id' => $admin->user_id, 'montantAdmin' => $montantAdmin]);

            // Créer une transaction pour l'administrateur
            $this->createTransaction(Auth::user()->id, $admin->user_id, 'Commission', $montantAdmin);

            Log::info('Transaction administrateur créée', ['admin_id' => $admin->user_id, 'montantAdmin' => $montantAdmin]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du portefeuille de l\'administrateur ou de la création de la transaction', [
                'admin_id' => $admin->user_id ?? 'Inconnu',
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Une erreur est survenue lors de la mise à jour du portefeuille de l\'administrateur.');
        }


        Notification::send($client, new colisaccept($data));
        Notification::send($fournisseur, new colisaccept($data));
        Notification::send($livreur, new colisaccept($data));

        Log::info('Notifications envoyées', ['client_id' => $client->id, 'fournisseur_id' => $fournisseur->id, 'livreur_id' => $livreur->id]);

        $this->notification->update(['reponse' => 'colisaccept']);
        Log::info('Notification mise à jour', ['notification_id' => $this->notification->id]);
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



    public function accepter($textareaContent = null)
    {
        // Récupérez le contenu du textarea depuis la requête
        $textareaContent = $textareaContent ?? '';

        // Vérifiez si l'utilisateur a un portefeuille
        $userId = Auth::id();
        $userWallet = Wallet::where('user_id', $userId)->first();
        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        // Mettez à jour la notification
        $notification = NotificationEd::find($this->notification->id);
        if (!$notification) {
            session()->flash('error', 'Notification introuvable.');
            return;
        }
        $notification->reponse = 'accepte';
        $notification->save();

        // Calculez le montant total et le code unique
        $requiredAmount = $this->notification->data['montantTotal'];
        $pourcentSomme = $requiredAmount * 0.1;
        $totalSom = $requiredAmount - $pourcentSomme;

        $code_livr = $this->code_unique ?? $this->genererCodeAleatoire(10);
        $produit = Produitservice::find($this->notification->data['idProd'] ?? $this->idProd2);

        // Préparez les données pour la notification
        $data = [
            'idProd' => $this->notification->data['idProd'] ?? $this->idProd2,
            'id_trader' => $this->userTrader ?? $this->notification->data['id_trader'],
            'totalSom' => $requiredAmount,
            'quantite' => $this->notification->data['quantité'] ?? $this->notification->data['quantiteC'],
            'localite' => $this->notification->data['localite'],
            'userSender' => $this->notification->data['userSender'] ?? $this->notification->data['id_sender'],
            'code_livr' => $code_livr,
            'prixProd' => $this->notification->data['prixTrade'] ?? $produit->prix,
            'textareaContent' => $textareaContent
        ];



        // Vérifiez si le code_unique existe dans userquantites
        $userQuantites = userquantites::where('code_unique', $code_livr)->get();

        // Log l'état initial de la récupération des données
        Log::info('Recherche du code_unique', ['code_unique' => $code_livr, 'count' => $userQuantites->count()]);

        if ($userQuantites->isNotEmpty()) {
            // Récupérez les IDs des utilisateurs et faites la somme des quantités
            $userQuantities = $userQuantites->groupBy('user_id')->map(function ($items) {
                return [
                    'quantite' => $items->sum('quantite'),
                    'type_achat' => $items->first()->type_achat
                ];
            });

            // Log le résultat de la somme des quantités et des types d'achat
            Log::info('Quantités groupées par utilisateur et type d\'achat', ['user_quantities' => $userQuantities]);

            // Traitez chaque utilisateur en fonction de leur type_achat
            foreach ($userQuantities as $userId => $info) {
                $typeAchat = $info['type_achat'];
                $quantite = $info['quantite'];

                if ($typeAchat === 'Take Away' || $typeAchat === 'Reservation') {
                    // Envoyez la notification au client directement
                    $userSender = User::find($userId);
                    if ($userSender) {
                        Notification::send($userSender, new AllerChercher($data));
                        // Log l'envoi de la notification
                        Log::info('Notification envoyée au client', ['user_id' => $userSender->id]);
                    } else {
                        // Log un avertissement si aucun utilisateur trouvé
                        Log::warning('Utilisateur non trouvé pour l\'ID', ['user_id' => $userId]);
                    }
                } else {
                    // Envoyez la notification aux livreurs
                    $livreurs = User::where('actor_type', 'livreur')->get();
                    foreach ($livreurs as $livreur) {
                        Notification::send($livreur, new livraisonVerif($data));
                        // Log l'envoi de la notification
                        Log::info('Notification envoyée au livreur', ['livreur_id' => $livreur->id]);
                    }
                }
            }
        } else {
            $livreurs = User::where('actor_type', 'livreur')->get();

            foreach ($livreurs as $livreur) {
                Notification::send($livreur, new livraisonVerif($data));
            }
        }

        session()->flash('success', 'Achat accepté.');

        $this->modalOpen = false;
        $this->notification->update(['reponse' => 'accepte']);
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

        $userId = Auth::id();
        if (!$userId) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $notification = NotificationEd::find($this->notification->id);
        $notification->reponse = 'refuser';
        $notification->save();

        // Récupérer les IDs d'expéditeur depuis la notification
        $senderIds = $this->notification->data['userSender'];
        $requiredAmount = $this->notification->data['montantTotal'];

        // Vérifier si l'ID d'expéditeur est un tableau ou un ID unique
        if (is_array($senderIds)) {
            // Cas où id_sender est un tableau d'IDs
            $userSenders = User::whereIn('id', $senderIds)->get();

            foreach ($userSenders as $userSender) {
                $userWallet = Wallet::where('user_id', $userSender->id)->first();

                if (!$userWallet) {
                    session()->flash('error', 'Portefeuille introuvable pour l\'utilisateur avec ID ' . $userSender->id);
                    return;
                }

                // Ajouter le montant requis au solde du portefeuille
                $userWallet->increment('balance', $requiredAmount);

                // Créer une transaction pour chaque utilisateur
                $this->createTransaction($userId, $userSender->id, 'Reception', $requiredAmount);

                // Envoyer une notification à chaque utilisateur
                Notification::send($userSender, new RefusAchat($this->messageR));
            }
        } else {
            // Cas où id_sender est un seul ID
            $userSender = User::find($senderIds);

            if ($userSender) {
                $userWallet = Wallet::where('user_id', $userSender->id)->first();

                if (!$userWallet) {
                    session()->flash('error', 'Portefeuille introuvable pour l\'utilisateur avec ID ' . $userSender->id);
                    return;
                }

                // Ajouter le montant requis au solde du portefeuille
                $userWallet->increment('balance', $requiredAmount);

                // Créer une transaction pour l'utilisateur
                $this->createTransaction($userId, $userSender->id, 'Reception', $requiredAmount);

                // Envoyer une notification à l'utilisateur
                Notification::send($userSender, new RefusAchat($this->messageR));
            } else {
                session()->flash('error', 'Utilisateur introuvable avec ID ' . $senderIds);
            }
        }

        $this->notification->update(['reponse' => 'refuser']);

        session()->flash('success', 'Achat refusé.');
        // $this->emit('notificationUpdated');
    }

    public function refuseVerif()
    {

        // Calcul du prix total
        $this->totalPrice = (int) ($this->notification->data['quantite'] * $this->notification->data['prixProd']) + $this->notification->data['prixTrade'];
        $montantTotal = $this->totalPrice;

        // Récupération des utilisateurs
        $livreur = User::find($this->notification->data['id_livreur']);
        $fournisseur = User::find($this->notification->data['id_trader']);

        // Récupération de l'utilisateur authentifié (client)
        $client = Auth::user();  // Remplace Auth::id() par Auth::user() pour obtenir l'objet User

        // Récupération du portefeuille du client
        $clientWallet = Wallet::where('user_id', $client->id)->first();

        if (!$clientWallet) {
            session()->flash('error', 'Portefeuille du client introuvable.');
            return;
        }

        // Augmentation du solde du portefeuille du client
        $clientWallet->increment('balance', $montantTotal);

        // Création de la transaction
        $this->createTransaction($this->notification->data['id_trader'], $client->id, 'Reception', $montantTotal);

        // Envoi des notifications
        Notification::send($livreur, new RefusVerif('Le colis a été refusé !'));
        Notification::send($fournisseur, new RefusVerif('Le colis a été refusé !'));
        Notification::send($client, new RefusVerif('Le colis a été refusé !'));

        // Mise à jour de la notification
        $this->notification->update(['reponse' => 'refuseVerif']);
        $this->validate();
    }
    public function refuseVerifLivreur()
    {

        // Calcul du prix total
        $this->totalPrice = (int) ($this->notification->data['quantite'] * $this->notification->data['prixProd']) + $this->notification->data['prixTrade'];
        $montantTotal = $this->totalPrice;

        // Récupération des utilisateurs
        $livreur = User::find($this->notification->data['id_livreur']);
        $fournisseur = User::find($this->notification->data['id_trader']);

        // Récupération de l'utilisateur authentifié (client)
        $client = $this->client;  // Remplace Auth::id() par Auth::user() pour obtenir l'objet User

        // Récupération du portefeuille du client
        $clientWallet = Wallet::where('user_id', $client->id)->first();

        if (!$clientWallet) {
            session()->flash('error', 'Portefeuille du client introuvable.');
            return;
        }

        // Augmentation du solde du portefeuille du client
        $clientWallet->increment('balance', $montantTotal);

        // Création de la transaction
        $this->createTransaction($this->notification->data['id_trader'], $client->id, 'Reception', $montantTotal);

        // Envoi des notifications
        Notification::send($livreur, new RefusVerif('Le colis a été refusé !'));
        Notification::send($fournisseur, new RefusVerif('Le colis a été refusé !'));
        Notification::send($this->client, new RefusVerif('Le colis a été refusé !'));

        // Mise à jour de la notification
        $this->notification->update(['reponse' => 'refuseVerif']);
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
            'specificite' => 'nullable|string',

        ]);

        $comment = Comment::create([
            'localite' => $this->notification->data['localite'],
            'specificite' => $this->specificite,
            'prixTrade' => $this->prixTrade,
            'id_sender' => json_encode($this->id_sender),
            'nameprod' => $this->nameprod,
            'code_unique' => $this->code_unique,
            'id_trader' => $this->id_trader,
            'quantiteC' => $this->quantiteC,
        ]);
        $this->commentsend($comment);

        broadcast(new CommentSubmitted($this->prixTrade,  $comment->id))->toOthers();

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

        $comment = Comment::create([
            'localite' => $this->notification->data['localite'],
            'specificite' => $this->specificite,
            'prixTrade' => $this->prixTrade,
            'id_sender' => json_encode($this->idsender),
            'nameprod' => $this->nameprod,
            'code_unique' => $this->code_unique,
            'id_trader' => $this->id_trader,
            'quantiteC' => $this->quantiteC,
        ]);

        $this->commentsend($comment);

        broadcast(new CommentSubmitted($this->prixTrade,  $comment->id))->toOthers();


        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $this->code_unique)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => $this->id_trader,
                'userSender' => $this->namefourlivr,
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
        $comment = Comment::create([
            'prixTrade' => $validatedData['prixTrade'],
            'code_unique' => $validatedData['code_livr'],
            'id_trader' => $validatedData['id_trader'],
            'quantiteC' => $validatedData['quantiteC'],
            'id_prod' => $validatedData['idProd'],
            'prixProd' => $validatedData['prixProd'],
        ]);
        $this->commentsend($comment);

        broadcast(new CommentSubmitted($validatedData['prixTrade'],  $comment->id))->toOthers();


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
        // $this->dispatch('priceSubmitted', $validatedData);
        // $this->dispatch('form-submitted');
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
        $comment = Comment::create([
            'prixTrade' => $validatedData['prixTrade'],
            'code_unique' => $validatedData['code_livr'],
            'id_trader' => $validatedData['id_trader'],
            'quantiteC' => $validatedData['quantiteC'],
            'id_prod' => $validatedData['idProd'],
        ]);

        $this->commentsend($comment);

        broadcast(new CommentSubmitted($validatedData['prixTrade'],  $comment->id))->toOthers();

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
    public function commentoffgroup()
    {
        try {
            // Récupérer l'utilisateur authentifié
            $this->validate([
                'prixTrade' => 'required|numeric',
                'id_trader' => 'required|numeric',
                'code_unique' => 'required|string',
                'idProd' => 'required|numeric',
            ]);


            // Création du commentaire
            $comment = Comment::create([
                'prixProd' => $this->prixTrade,
                'prixTrade' => $this->prixTrade,
                'id_trader' => $this->id_trader,
                'code_unique' => $this->code_unique,
                'id_prod' => $this->idProd,
            ]);
            $this->commentsend($comment);

            broadcast(new CommentSubmitted($this->prixTrade,  $comment->id))->toOthers();

            $produit = ProduitService::with('user')->find($this->idProd);

            if ($produit) {
                $userId = $produit->user_id; // Directement depuis l'objet ProduitService
            }
            // Vérifier si un compte à rebours est déjà en cours pour cet code unique
            $existingCountdown = Countdown::where('code_unique', $this->code_unique)
                ->where('notified', false)
                ->orderBy('start_time', 'desc')
                ->first();

            if (!$existingCountdown) {
                // Créer un nouveau compte à rebours s'il n'y en a pas en cours
                Countdown::create([
                    'user_id' => $this->id_trader,
                    'userSender' => $userId,
                    'start_time' => now(),
                    'code_unique' => $this->code_unique,
                    'difference' => 'offredirect',
                ]);
            }
            $this->reset(['prixTrade']);
        } catch (\Exception $e) {
            // dd($e)->getMessage();
            // En cas d'erreur, redirection avec un message d'erreur
            return redirect()->back()->with('error', 'Erreur lors de la soumission de l\'offre: ' . $e->getMessage());
        }
    }


    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage($event)
    {
        // Déboguer pour vérifier la structure de l'événement
        // dd($event);

        // Récupérer les données de l'événement
        $commentId = $event['commentId'] ?? null;

        if ($commentId) {
            // Récupérer le commentaire par ID
            $comment = Comment::with('user')->find($commentId);

            if ($comment) {
                // Ajouter le nouveau commentaire à la liste
                $this->commentsend($comment);
            } else {
                // Gérer le cas où le commentaire n'existe pas
                Log::error('Commentaire non trouvé', ['commentId' => $commentId]);
            }
        } else {
            // Gestion des erreurs si l'ID du commentaire n'est pas fourni
            Log::error('ID du commentaire manquant dans l\'événement', ['event' => $event]);
        }
    }

    public function commentsend($comment)
    {
        if ($comment) {
            $this->comments[] = [
                'prix' => $comment->prixTrade,
                'commentId' => $comment->id,
                'nameUser' => $comment->user->name,
                'photoUser' => $comment->user->photo,
            ];
        }
    }

    public function render()
    {
        return view('livewire.notification-show');
    }
}
