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
use App\Notifications\acceptAchat;
use App\Notifications\colisaccept;
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
        $this->roiDeposit = $this->notification->data['roi']?? null;
        $this->userDeposit = User::find($this->notification->data['user_id']?? null);

        $userCennecedid = Auth::user()->id;

        $this->userConnected = User::find($userCennecedid);

        $this->id_sos = $this->notification->data['id_sos']?? null;

        $this->operatorRecu = $this->notification->data['operator']?? null;

        $this->phonenumberRecu = $this->notification->data['phonenumber']?? null;

        // Vérifiez si la demande existe déjà dans la table RechargeSos
        $this->existingRequest = RechargeSos::where('id_sos', $this->id_sos)->first();

        


        $this->montantTotal = $this->notification->data['montantTotal'] ?? null;
        $this->userSender = $this->notification->data['userSender'] ?? [];
        $this->notifId = $this->notification->id;
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->userTrader = $this->notification->data['userTrader'] ?? null;
        $this->id_trader = Auth::user()->id ?? null;
        $this->user = Auth::user()->id ?? null;
        $this->code_unique = $this->notification->data['code_unique'] ?? $this->notification->data['Uniquecode'] ?? null;
        $this->quantite = $this->notification->data['quantité'] ?? null;
        $this->quantiteC = $this->notification->data['quantite'] ?? $this->notification->data['quantity'] ?? $this->notification->data['quantites'] ?? null;
        $this->localite = $this->notification->data['localite'] ?? null;
        $this->specificite = $this->notification->data['specificity'] ?? null;
        $this->userFour = User::find($this->notification->data['fournisseur'] ?? null);
        $this->code_livr = $this->notification->data['code_livr'] ?? null;

        $this->psap = $this->notification->data['psap'] ?? null;

        $this->amount = $this->notification->data['amount'] ?? null;

        $this->userId = $this->notification->data['userId'] ?? null;

        $this->demandeur = User::find($this->notification->data['userId'] ?? null);



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
            || $this->notification->type === 'App\Notifications\AppelOffreTerminerGrouper'
            || $this->notification->type === 'App\Notifications\AppelOffre'
            || $this->notification->type === 'App\Notifications\OffreNotifGroup'
            || $this->notification->type === 'App\Notifications\NegosTerminer'
            || $this->notification->type === 'App\Notifications\OffreNegosNotif'
            || $this->notification->type === 'App\Notifications\OffreNegosDone'
            || $this->notification->type === 'App\Notifications\AOGrouper'
            ||  $this->notification->type === 'App\Notifications\OffreNotif'
            || $this->notification->type === 'App\Notifications\Retrait')
            || $this->notification->type === 'App\Notifications\DepositSos' 
            || $this->notification->type == 'App\Notifications\DepositRecu'
            || $this->notification->type === 'App\Notifications\DepositSend'
            ? null
            : (ProduitService::find($this->notification->data['idProd']) ?? $this->notification->data['produit_id'] ?? null) ?? null;

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

        // $comments = Comment::with('user')
        //     ->where('code_unique', $codeUnique)
        //     ->whereNotNull('prixTrade')
        //     ->orderBy('prixTrade', 'asc')
        //     ->get();
        // foreach ($comments as $comment) {
        //     $this->commentsend($comment);
        // }

        // Récupérer le commentaire de l'utilisateur connecté
        $this->userComment = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->where('id_trader', $this->user)
            ->first();

        // Compter le nombre de commentaires
        // $this->commentCount = $comments->count();

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Comment::where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('created_at', 'asc')
            ->first();

        // Initialiser la variable pour la date du plus ancien commentaire
        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment ? $this->oldestComment->created_at->toIso8601String() : null;
        $this->serverTime = Carbon::now()->toIso8601String();

        // Ajouter 5 heures à la date la plus ancienne, s'il y en a une
        $this->tempsEcoule = $this->oldestCommentDate ? Carbon::parse($this->oldestCommentDate)->addMinutes(1) : null;

        // Vérifier si $tempsEcoule est écoulé
        $this->isTempsEcoule = $this->tempsEcoule && $this->tempsEcoule->isPast();


        // Recherche dans la table produit_service pour récupérer l'ID du produit
        // if (isset($this->notification->data['nameprod']) && isset($this->notification->data['id_trader'])) {
        //     $produitService = ProduitService::where('name', $this->notification->data['nameprod'])
        //         ->where('user_id', $this->notification->data['id_trader'])
        //         ->first();

        //     if ($produitService) {
        //         $this->idProd2 = $produitService->id;
        //     }
        // }
        //offre ajout de quantite
        // $this->offreinfo($this->appelOffreGroup);

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



        //ciblage de livreur
        // $this->nombreLivr = User::where('actor_type', 'livreur')->count();

        // $this->ciblageLivreurs();

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

            $this->createTransaction($this->demandeur->id, Auth::id(), 'Reception', $this->amount);
            $this->createTransaction($this->demandeur->id,  Auth::id(), 'withdrawal', $this->amount);

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

            $this->offreinfo($offregroupe);

            Log::info('Enregistrement dans AppelOffreGrouper sauvegardé.', ['offregroupe_id' => $offregroupe->id]);
            broadcast(new AjoutQuantiteOffre($validatedData['quantite']))->toOthers();


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
            // $this->dispatch('Ajoutquantite');
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

        $this->validate([
            'dateTot' => 'required|date',
            'dateTard' => 'required|date|after_or_equal:dateTot',
            'timeStart' => 'required',
            'timeEnd' => 'required|after:timeStart',
            'dayPeriod' => 'required|string',
        ]);




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
                        'timeStart' => $this->timeStart,
                        'date_tot' => $this->dateTot,
                        'date_tard' => $this->dateTard,
                        'timeEnd' => $this->timeEnd,
                        'dayPeriod' => $this->dayPeriod,
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

    protected function handlePhotoUpload($photoField)
    {
        if ($this->$photoField) {
            Log::info("Début du téléchargement pour le champ photo : {$photoField}");

            // Récupérer le fichier photo
            $photo = $this->$photoField;
            $photoName = time() . '.' . $photo->getClientOriginalExtension(); // Générer un nom de fichier unique

            // Définir le chemin où stocker l'image
            $path = public_path('post/'); // Chemin public/post/

            // Vérifier si le dossier 'post' existe, sinon le créer
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                Log::info("Dossier 'post' créé avec succès.");
            }

            // Tenter de déplacer le fichier
            try {
                $photo->storeAs('post', $photoName, 'public'); // Utiliser le système de stockage Laravel
                Log::info("Photo téléchargée avec succès : {$photoName}");
            } catch (\Exception $e) {
                Log::error("Erreur lors du déplacement de l'image : " . $e->getMessage());
                throw new \Exception("Erreur lors du téléchargement de l'image : " . $e->getMessage());
            }

            // Retourner le chemin de l'image
            return 'post/' . $photoName;
        }

        Log::warning("Aucune photo trouvée pour le champ : {$photoField}");
        return null;
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
                'fournisseur' => $this->notification->data['fournisseur'],
                'localité' => $this->localite,
                'quantite' => $quantite,
                'livreur' => $this->notification->data['livreur'] ?? null, // Assurez-vous que $this->userFour est défini
                'prixTrade' => $this->notification->data['prixTrade'] ?? null,
                'prixProd' => $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'],
                'date_tot' => $this->notification->data['date_tot'],
                'date_tard' => $this->notification->data['date_tard'],
            ];

            $user = [
                'idProd' => $this->notification->data['idProd'],
                'code_unique' => $this->code_unique ?? $this->notification->data['code_livr'],
                'fournisseur' => $this->userFour->id ?? $this->notification->data['fournisseur'],
                'localité' => $this->localite,
                'quantite' => $quantite,
                'id_client' => Auth::id(),
                'prixProd' => $this->notification->data['prixProd'],
                'date_tot' => $this->notification->data['date_tot'],
                'date_tard' => $this->notification->data['date_tard'],
            ];

            $id_trader =  $this->notification->data['fournisseur'];
            $traderUser = User::find($id_trader);

            Log::info('Notification envoyée au userSender', ['userId' => $userSender->id, 'data' => $data]);

            if ($this->notification->type_achat == 'reserv/take' || $this->notification->type == 'App\Notifications\AllerChercher') {
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

        if ($userQuantitiesExist && $this->notification->type_achat !== 'reserv/take') {
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
            'fournisseur' => $this->notification->data['fournisseur'],
            'localité' => $this->localite,
            'quantite' => $this->quantiteC,
            'id_client' => $this->notification->data['id_client'],
            'livreur' => $id_livreur,
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

        $livreur = User::find($this->notification->data['livreur']);
        $fournisseur = User::find($this->notification->data['fournisseur']);
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
            'fournisseur' => $this->notification->data['fournisseur'],
            'localité' =>  $this->notification->data['localité'],
            'quantite' => $this->notification->data['quantite'],
            'id_client' => $this->notification->data['id_client'],
            'livreur' => $this->notification->data['livreur'],
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

        $fournisseurWallet = Wallet::where('user_id', $this->notification->data['fournisseur'])->first();
        if (!$fournisseurWallet) {
            Log::error('Portefeuille du fournisseur introuvable', ['user_id' => $this->notification->data['fournisseur']]);
            session()->flash('error', 'Portefeuille du fournisseur introuvable.');
            return;
        }

        $livreurWallet = Wallet::where('user_id', $this->notification->data['livreur'])->first();
        if (!$livreurWallet) {
            Log::error('Portefeuille du livreur introuvable', ['user_id' => $this->notification->data['livreur']]);
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
        $this->createTransaction(Auth::user()->id, $this->notification->data['fournisseur'], 'Reception', $totalSom);
        Log::info('Transaction fournisseur créée', ['fournisseur_id' => $fournisseur->id, 'totalSom' => $totalSom]);

        // Montant total de la transaction
        $prixTotal = $this->notification->data['prixTrade'];
        $montantAdmin = $prixTotal * 0.10;
        $montantLivreur = $prixTotal - $montantAdmin;

        $livreurWallet->increment('balance', $montantLivreur);
        Log::info('Solde du portefeuille du livreur mis à jour', ['livreur_id' => $livreur->id, 'montantLivreur' => $montantLivreur]);

        $this->createTransaction(Auth::user()->id, $this->notification->data['livreur'], 'Reception', $montantLivreur);
        Log::info('Transaction livreur créée', ['livreur_id' => $livreur->id, 'montantLivreur' => $montantLivreur]);


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
    // public function accepter($textareaContent = null)
    // {
    //     // Récupérez le contenu du textarea depuis la requête
    //     $textareaContent = $textareaContent ?? '';

    //     // Vérifiez si l'utilisateur a un portefeuille
    //     $userId = Auth::id();
    //     $userWallet = Wallet::where('user_id', $userId)->first();
    //     if (!$userWallet) {
    //         session()->flash('error', 'Portefeuille introuvable.');
    //         return;
    //     }

    //     // Mettez à jour la notification
    //     $notification = NotificationEd::find($this->notification->id);
    //     if (!$notification) {
    //         session()->flash('error', 'Notification introuvable.');
    //         return;
    //     }
    //     $notification->reponse = 'accepte';
    //     $notification->save();

    //     // Calculez le montant total et le code unique
    //     $requiredAmount = $this->notification->data['montantTotal'];
    //     $pourcentSomme = $requiredAmount * 0.1;
    //     $totalSom = $requiredAmount - $pourcentSomme;

    //     $code_livr = $this->code_unique ?? $this->genererCodeAleatoire(10);
    //     $produit = Produitservice::find($this->notification->data['idProd'] ?? $this->idProd2);

    //     // Préparez les données pour la notification
    //     $data = [
    //         'idProd' => $this->notification->data['idProd'] ?? $this->idProd2,
    //         'id_trader' => $this->userTrader ?? $this->notification->data['id_trader'],
    //         'totalSom' => $requiredAmount,
    //         'quantites' => $this->notification->data['quantité'] ?? $this->notification->data['quantiteC'],
    //         'localite' => $this->notification->data['localite'],
    //         'userSender' => $this->notification->data['userSender'] ?? $this->notification->data['id_sender'],
    //         'code_livr' => $code_livr,
    //         'prixProd' => $this->notification->data['prixTrade'] ?? $produit->prix,
    //         'textareaContent' => $textareaContent
    //     ];
    //     Log::info('data', ['data' => $data]);

    //     // Vérifiez si le code_unique existe dans userquantites
    //     $userQuantites = userquantites::where('code_unique', $code_livr)->get();

    //     // Log l'état initial de la récupération des données
    //     Log::info('Recherche du code_unique', ['code_unique' => $code_livr, 'count' => $userQuantites->count()]);

    //     if ($userQuantites->isNotEmpty()) {
    //         foreach ($userQuantites as $userQuantite) {
    //             $userId = $userQuantite->user_id;
    //             $quantite = $userQuantite->quantite;
    //             $typeAchat = $userQuantite->type_achat;

    //             $notificationData = array_merge($data, [
    //                 'quantite' => $quantite,
    //                 'type_achat' => $typeAchat,
    //                 'user_id' => $userId,
    //             ]);

    //             if ($typeAchat === 'Take Away' || $typeAchat === 'Reservation') {
    //                 // Envoyez la notification au client directement
    //                 $userSender = User::find($userId);
    //                 if ($userSender) {
    //                     Notification::send($userSender, new AllerChercher($notificationData));
    //                     // Log l'envoi de la notification
    //                     Log::info('Notification envoyée au client', ['user_id' => $userSender->id]);
    //                 } else {
    //                     // Log un avertissement si aucun utilisateur trouvé
    //                     Log::warning('Utilisateur non trouvé pour l\'ID', ['user_id' => $userId]);
    //                 }
    //             } else {
    //                 // Envoyez la notification aux livreurs
    //                 if (!empty($this->livreursIds)) {
    //                     foreach ($this->livreursIds as $livreurId) {
    //                         $livreur = User::find($livreurId);
    //                         if ($livreur) {
    //                             Notification::send($livreur, new livraisonVerif($notificationData));
    //                             // Log l'envoi de la notification
    //                             Log::info('Notification envoyée au livreur', ['livreur_id' => $livreur->id]);
    //                         } else {
    //                             // Log un avertissement si aucun livreur trouvé
    //                             Log::warning('Livreur non trouvé pour l\'ID', ['livreur_id' => $livreurId]);
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     } else {
    //         // Si aucune quantité utilisateur trouvée, envoyez des notifications aux livreurs seulement
    //         if (!empty($this->livreursIds)) {
    //             foreach ($this->livreursIds as $livreurId) {
    //                 $livreur = User::find($livreurId);
    //                 if ($livreur) {
    //                     Notification::send($livreur, new livraisonVerif($data));
    //                     // Log l'envoi de la notification
    //                     Log::info('Notification envoyée au livreur', ['livreur_id' => $livreur->id]);
    //                 } else {
    //                     // Log un avertissement si aucun livreur trouvé
    //                     Log::warning('Livreur non trouvé pour l\'ID', ['livreur_id' => $livreurId]);
    //                 }
    //             }
    //         }
    //     }

    //     session()->flash('success', 'Achat accepté.');

    //     $this->modalOpen = false;
    //     $this->notification->update(['reponse' => 'accepte']);
    // }



    // private function genererCodeAleatoire($longueur)
    // {
    //     $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    //     $code = '';

    //     for ($i = 0; $i < $longueur; $i++) {
    //         $code .= $caracteres[rand(0, strlen($caracteres) - 1)];
    //     }

    //     return $code;
    // }

    public function refuserPro()
    {
        try {
            // Mise à jour de la notification
            $this->notification->update(['reponse' => 'refuser']);

            // Récupération de l'utilisateur Trader
            $userTrader = User::findOrFail($this->notification->data['id_trader']);

            // Envoi de la notification
            Notification::send($userTrader, new RefusAchat($this->messageR));
        } catch (Exception $e) {
            // Gérer l'exception, enregistrer l'erreur dans les logs et afficher un message d'erreur
            Log::error('Erreur lors du refus de la proposition : ' . $e->getMessage());

            // Vous pouvez ajouter un retour ou une redirection avec un message d'erreur
            return back()->with('error', 'Une erreur s\'est produite lors du refus de la proposition.');
        }
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
        $senderIds = $this->notification->data['userSender'] ?? null;
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

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }



    public function render()
    {
        return view('livewire.notification-show');
    }
}
