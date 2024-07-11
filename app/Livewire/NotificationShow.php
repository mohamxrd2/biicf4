<?php

namespace App\Livewire;

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
use App\Models\NotificationEd;
use App\Models\ProduitService;
use Illuminate\Support\Carbon;
use App\Models\NotificationLog;
use App\Notifications\AppelOffre;
use App\Notifications\RefusAchat;

use App\Notifications\acceptAchat;
use App\Notifications\commandVerif;
use Illuminate\Support\Facades\Log;
use App\Notifications\NegosTerminer;
use Illuminate\Support\Facades\Auth;
use App\Notifications\livraisonVerif;
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

    public $userTrader;
    public $code_unique;
    public $id_trader;
    public $prixTrade;
    public $secondsRemaining = 60; // 60 seconds = 1 minute
    public $timerInterval;

    public $quantite;
    public $quantiteC;

    public $localite;

    public $modalOpen = false;

    public $userFour = null;
    public $code_livr;
    public $countdownStarted = false;
    public $timeRemaining = null;
    public $endTime = null;

    public $nameSender;
    public $namefourlivr;
    public $comments;
    public $userComment;
    public $commentCount;
    public $oldestCommentDate;
    public $isTempsEcoule;
    public $tempsEcoule;
    public $oldestComment;
    public $nombreLivr;

    //test
    public $countdownTime;
    public $timerStarted;
    public $timeleft;
    public $produitfat;

    public $totalPrice;

    protected $listeners = ['startTimer'];


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
        $this->quantiteC = $this->notification->data['quantite'] ?? null;
        $this->localite = $this->notification->data['localite'] ?? null;
        $this->userFour = User::find($this->notification->data['id_trader'] ?? null);
        $this->code_livr = $this->notification->data['code_livr'] ?? null;
        $this->nameSender = $this->notification->data['userSender'] ?? null;
        //pour la facture
        $this->produitfat = ProduitService::find($this->notification->data['idProd']);
        $this->idProd = $this->notification->data['idProd'];

        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);

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
    }



    public function valider()
    {
        //prix final
        $this->totalPrice = (int) ($this->notification->data['quantiteC'] * $this->produitfat->prix) + $this->notification->data['prixTrade'];

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
            'id_trader' => $this->namefourlivr->id,
        ];
        

        Notification::send($userSender, new commandVerif($data));

        $this->notification->update(['reponse' => 'valide']);
        $this->validate();
    }



    public function accepterAGrouper()
    {
        $this->notification->update(['reponse' => 'accepte']);
        $this->validate();

        $userId = Auth::id();
        $userWallet = Wallet::where('user_id', $userId)->first();

        try {
            // Trouver et mettre à jour la notification
            $notification = DatabaseNotification::find($this->notification->id);
            if ($notification) {
                $notification->reponse = 'accepte';
                $notification->save();
            } else {
                throw new Exception('Notification non trouvée.');
            }

            // Calcul du pourcentage et du montant total
            $pourcentSomme = $this->montantTotal * 0.1;
            $totalSom = $this->montantTotal - $pourcentSomme;

            // Incrémenter le solde du portefeuille de userTrader
            $userWallet->increment('balance', $totalSom);

            // Créer une transaction de réception
            $this->createTransaction($this->userSender[0], $userId, 'Reception', $totalSom);

            // Traitement pour le parrain du trader
            $userTrader = User::find($userId);
            if ($userTrader->parrain) {
                $commTraderParrain = $pourcentSomme * 0.05;
                $commTraderParrainWallet = Wallet::where('user_id', $userTrader->parrain)->first();
                if ($commTraderParrainWallet) {
                    $commTraderParrainWallet->increment('balance', $commTraderParrain);
                    $this->createTransaction($userId, $userTrader->parrain, 'Commission', $commTraderParrain);
                } else {
                    throw new Exception('Portefeuille du parrain pour l\'utilisateur ID ' . $userTrader->parrain->id . ' introuvable.');
                }
            }

            // Traitement pour chaque utilisateur ayant envoyé la demande
            foreach ($this->userSender as $userSenderId) {
                $senderWallet = Wallet::where('user_id', $userSenderId)->first();
                if (!$senderWallet) {
                    throw new Exception('Portefeuille pour l\'utilisateur ID ' . $userSenderId . ' introuvable.');
                }
                $senderWallet->increment('balance', $this->montantTotal);
                $this->createTransaction($userSenderId, $userId, 'Envoie', $this->montantTotal);

                $userSender = User::find($userSenderId);
                if (!$userSender) {
                    throw new Exception('Utilisateur ID ' . $userSenderId . ' introuvable.');
                }

                if ($userSender->parrain) {
                    $commSenderParrain = $pourcentSomme * 0.05;
                    $commSenderParrainWallet = Wallet::where('user_id', $userSender->parrain)->first();
                    if ($commSenderParrainWallet) {
                        $commSenderParrainWallet->increment('balance', $commSenderParrain);
                        $this->createTransaction($userSenderId, $userSender->parrain, 'Commission', $commSenderParrain);
                    } else {
                        throw new Exception('Portefeuille du parrain pour l\'utilisateur ID ' . $userSender->parrain->id . ' introuvable.');
                    }
                }

                Notification::send($userSender, new AcceptAchat($this->messageA));
            }

            // Supprimer les logs de notification pour le produit spécifié
            NotificationLog::where('idProd', $this->idProd)->delete();

            session()->flash('success', 'Action acceptée avec succès');
            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'acceptation de l\'achat:', ['exception' => $e]);
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function refuserAGrouper()
    {
        $this->validate();

        try {
            // Update the notification response to "refuser"
            $this->notification->update(['reponse' => 'refuser']);

            foreach ($this->userSender as $userSenderId) {
                $userSenderWallet = Wallet::where('user_id', $userSenderId)->first();
                if (!$userSenderWallet) {
                    throw new Exception('Portefeuille pour l\'utilisateur ID ' . $userSenderId . ' introuvable.');
                }
                // Increment the user wallet balance by the total amount
                $userSenderWallet->increment('balance', $this->montantTotal);

                // Create a transaction
                $transaction = new Transaction();
                $transaction->sender_user_id = Auth::id();
                $transaction->receiver_user_id = $userSenderId;
                $transaction->type = 'Reception';
                $transaction->amount = $this->montantTotal;
                $transaction->save();

                // Retrieve the user sender
                $userSender = User::find($userSenderId);
                if (!$userSender) {
                    throw new Exception('Utilisateur ID ' . $userSenderId . ' introuvable.');
                }

                // Send refusal notification
                Notification::send($userSender, new RefusAchat($this->messageR));
            }

            // Delete the notification logs for the specified product
            NotificationLog::where('idProd', $this->idProd)->delete();

            session()->flash('success', 'Refus traité avec succès.');
            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Erreur lors du traitement du refus:', ['exception' => $e]);
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
            return redirect()->back();
        }
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

        $code_livr = $this->genererCodeAleatoire(10);

        // $userTrader = User::find($userId);
        // $this->handleCommission($userTrader, $pourcentSomme, 'Trader');
        // $this->handleCommission($userSender, $pourcentSomme, 'Sender');

        // $userWallet->increment('balance', $totalSom);
        // $this->createTransaction($userSender->id, $userId, 'Reception', $totalSom);
        // $this->createTransaction($userSender->id, $userId, 'Envoie', $requiredAmount);

        // Notification::send($userSender, new acceptAchat($this->messageA));

        $data = [
            'idProd' => $this->notification->data['idProd'],
            'id_trader' => $this->userTrader,
            'totalSom' => $requiredAmount,
            'quantite' => $this->notification->data['quantité'],
            'localite' =>  $this->notification->data['localite'],
            'userSender' =>  $this->notification->data['userSender'],
            'code_livr' => $code_livr,

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

    protected function handleCommission($user, $amount, $type)
    {
        if ($user->parrain) {
            $commission = $amount * 0.05;
            $parrainWallet = Wallet::where('user_id', $user->parrain)->first();
            $parrainWallet->increment('balance', $commission);
            $this->createTransaction($user->id, $user->parrain, 'Commission', $commission);
        }
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

    public function commentForm()
    {
        // Récupérer l'utilisateur authentifié
        $this->validate([

            'id_trader' => 'required|numeric',
            'code_unique' => 'required|string',
            'prixTrade' => 'required|numeric',

        ]);

        Comment::create([
            'prixTrade' => $this->prixTrade,
            'code_unique' => $this->code_unique,
            'id_trader' => $this->id_trader,
        ]);

        session()->flash('success', 'Commentaire créé avec succès!');
        $this->dispatch('start-timer');

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
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $this->userSender,
                'start_time' => now(),
                'code_unique' => $validatedData['code_livr'],
            ]);

            $this->countdownStarted = true;
        }


        // Afficher un message de succès
        session()->flash('success', 'Commentaire créé avec succès!');

        // Réinitialiser le champ du formulaire
        $this->reset(['prixTrade']);

        $this->timerStarted = true;
        $this->dispatch('start-timer', ['countdowtime' => $this->countdownTime]);
    }


    // #[On('sendNotification')]
    public function render()
    {
        // Récupérer l'utilisateur authentifié
        // $user = Auth::user();


        // Récupérer la notification
        // $notification = DatabaseNotification::findOrFail($this->id);

        // Marquer la notification comme lue
        // if ($notification->unread()) {
        //     $notification->markAsRead();
        // }

        // Initialiser la variable produit à null
        // $produtOffre = null;
        // $notificationExists = null;
        // $oldestNotificationDate = null;
        // $sommeQuantites = null;
        // $nombreParticp = null;
        // $produit = null;
        // $prixArticleNegos = null;
        // $lowPriceComment = null;
        // $lowPriceUserName = null;
        // $lowPriceAmount = null;
        // $highestPricedComment = null;
        // $userFour = null;

        // Vérifier si 'produit_id' existe dans les données de notification
        // if (isset($notification->data['produit_id'])) {
        //     // Récupérer le produit associé à la notification
        //     $produtOffre = ProduitService::find($notification->data['produit_id']);
        // }

        // Vérifier si 'code_unique' existe dans les données de notification
        // $codeUnique = $notification->data['code_unique'] ?? $notification->data['code_livr'] ?? null;


        // Récupérer les commentaires avec code_unique et prixTrade non nul
        // $comments = Comment::with('user')
        //     ->where('code_unique', $codeUnique)
        //     ->whereNotNull('prixTrade')
        //     ->orderBy('prixTrade', 'asc')
        //     ->get();

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        // $oldestComment = Comment::where('code_unique', $codeUnique)
        //     ->whereNotNull('prixTrade')
        //     ->orderBy('created_at', 'asc')
        //     ->first();

        // // Initialiser la variable pour la date du plus ancien commentaire
        // $oldestCommentDate = $oldestComment ? $oldestComment->created_at : null;

        // // Ajouter 5 heures à la date la plus ancienne, s'il y en a une
        // $tempsEcoule = $oldestCommentDate ? Carbon::parse($oldestCommentDate)->addMinutes(1) : null;

        // // Vérifier si $tempsEcoule est écoulé
        // $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

        // // Récupérer le commentaire de l'utilisateur connecté
        // $userComment = Comment::with('user')
        //     ->where('code_unique', $codeUnique)
        //     ->where('id_trader', $user->id)
        //     ->first();

        // // Compter le nombre de commentaires
        // $commentCount = $comments->count();

        // Vérifier si le temps est écoulé /////\\\///////

        // $nombreLivr = User::where('actor_type', 'livreur')->count();


        // if ($notification->type === 'App\Notifications\AppelOffre') {
        //     Log::info('Notification type is App\Notifications\AppelOffre');

        //     $notificationExists = NotificationLog::where('code_unique', $codeUnique)->exists();
        //     $lowPriceComment = Comment::where('code_unique', $codeUnique)
        //         ->whereNotNull('prixTrade')
        //         ->orderBy('prixTrade', 'asc')
        //         ->orderBy('created_at', 'desc')
        //         ->first();

        //     if ($lowPriceComment) {
        //         Log::info('Low price comment found', ['lowPriceComment' => $lowPriceComment]);

        //         $data = [
        //             'prix_trade' => $lowPriceComment->prixTrade ?? null,
        //             'id_trader' => $lowPriceComment->id_trader ?? null,
        //             'id_prod' => $lowPriceComment->id_prod ?? null,
        //             'quantite' => $notification->data['quantity'] ?? null,
        //             'name' => $notification->data['productName'] ?? 'Produit sans nom'
        //         ];
        //         $lowPriceUserName = $lowPriceComment->user->name;
        //         $lowPriceAmount = $lowPriceComment->prixTrade;

        //         if ($isTempsEcoule && !$notificationExists) {
        //             Log::info('Time has elapsed and no notification exists', ['code_unique' => $codeUnique]);

        //             // Vérifier si 'id_sender' est un tableau ou un seul élément
        //             $idSenders = is_array($notification->data['id_sender']) ? $notification->data['id_sender'] : [$notification->data['id_sender']];

        //             foreach ($idSenders as $userSender) {
        //                 $owner = User::find($userSender);
        //                 Log::info('Processing user sender', ['userSender' => $userSender, 'owner' => $owner]);

        //                 if ($owner && $data['prix_trade'] && $data['quantite']) {
        //                     $prixArticle = $data['quantite'] * $data['prix_trade'];
        //                     Log::info('Price article calculated', ['prixArticle' => $prixArticle]);

        //                     // Trouver les portefeuilles du propriétaire et du trader
        //                     $ownerWallet = Wallet::where('user_id', $owner->id)->first();
        //                     $traderWallet = Wallet::where('user_id', $data['id_trader'])->first();
        //                     Log::info('Wallets found', ['ownerWallet' => $ownerWallet, 'traderWallet' => $traderWallet]);

        //                     if ($ownerWallet && $traderWallet) {
        //                         // Décrémenter le portefeuille du trader
        //                         $traderWallet->decrement('balance', $prixArticle);

        //                         // Incrémenter le portefeuille du propriétaire
        //                         $ownerWallet->increment('balance', $prixArticle);

        //                         // Enregistrer la transaction d'envoi
        //                         $transaction1 = new Transaction();
        //                         $transaction1->sender_user_id = $owner->id;
        //                         $transaction1->receiver_user_id = $data['id_trader'];
        //                         $transaction1->type = 'Envoie';
        //                         $transaction1->amount = $prixArticle;
        //                         $transaction1->save();

        //                         // Enregistrer la transaction de réception
        //                         $transaction2 = new Transaction();
        //                         $transaction2->sender_user_id = $owner->id;
        //                         $transaction2->receiver_user_id = $data['id_trader'];
        //                         $transaction2->type = 'Reception';
        //                         $transaction2->amount = $prixArticle;
        //                         $transaction2->save();

        //                         // Envoyer la notification à l'utilisateur authentifié
        //                         Notification::send($owner, new AppelOffreTerminer($data));
        //                         Log::info('Notification sent', ['user' => $owner, 'data' => $data]);

        //                         NotificationLog::create(['code_unique' => $codeUnique]);
        //                     } else {
        //                         // Gérer le cas où le portefeuille du propriétaire ou du trader n'est pas trouvé
        //                         if (!$ownerWallet) {
        //                             Log::error('Portefeuille non trouvé pour l\'utilisateur ID: ' . $owner->id);
        //                         }
        //                         if (!$traderWallet) {
        //                             Log::error('Portefeuille non trouvé pour le trader ID: ' . $data['id_trader']);
        //                         }
        //                     }
        //                 } else {
        //                     // Gérer le cas où le propriétaire ou les données requises sont manquants
        //                     Log::error('Propriétaire non trouvé ou données manquantes pour userSender ID: ' . $userSender);
        //                 }
        //             }
        //         }
        //     } else {
        //         $lowPriceUserName = 'N/A';
        //         $lowPriceAmount = 0;
        //     }
        // } elseif ($notification->type === 'App\Notifications\OffreNotifGroup') {
        //     Log::info('Notification type is OffreNotifGroup');

        //     $comments = Comment::with('user')
        //         ->where('code_unique', $codeUnique)
        //         ->whereNotNull('prixTrade')
        //         ->orderBy('prixTrade', 'desc')
        //         ->get();
        //     $oldestComment = Comment::where('code_unique', $codeUnique)
        //         ->whereNotNull('prixTrade')
        //         ->orderBy('created_at', 'desc')
        //         ->first();

        //     $oldestCommentDate = $oldestComment ? $oldestComment->created_at : null;
        //     $tempsEcoule = $oldestCommentDate ? Carbon::parse($oldestCommentDate)->addMinutes(1) : null;
        //     $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

        //     Log::info('Oldest comment date', ['oldestCommentDate' => $oldestCommentDate]);
        //     Log::info('Temps écoulé', ['tempsEcoule' => $tempsEcoule, 'isTempsEcoule' => $isTempsEcoule]);

        //     $notificationExists = NotificationLog::where('code_unique', $codeUnique)->exists();
        //     Log::info('Notification exists', ['exists' => $notificationExists]);

        //     if ($isTempsEcoule && !$notificationExists) {
        //         Log::info('Time has elapsed and no notification exists', ['code_unique' => $codeUnique]);

        //         $highestPricedComment = Comment::where('code_unique', $codeUnique)
        //             ->whereNotNull('prixTrade')
        //             ->orderBy('prixTrade', 'desc')
        //             ->orderBy('created_at', 'asc')
        //             ->first();
        //         Log::info('Highest priced comment', ['highestPricedComment' => $highestPricedComment]);

        //         if ($highestPricedComment) {
        //             $highestPricedCommentUser = $highestPricedComment->user;
        //             $highestPricedCommentUserName = $highestPricedCommentUser->name;
        //             Log::info('Highest priced comment user', ['user' => $highestPricedCommentUser]);

        //             $ownerWallet = Wallet::where('user_id', $highestPricedCommentUser->id)->first();
        //             $traderWallet = Wallet::where('user_id', $produtOffre->id)->first();
        //             Log::info('Wallets found', ['ownerWallet' => $ownerWallet, 'traderWallet' => $traderWallet]);

        //             if ($ownerWallet && $traderWallet) {
        //                 $ownerWallet->decrement('balance', $highestPricedComment->prixTrade);
        //                 $traderWallet->increment('balance', $highestPricedComment->prixTrade);

        //                 $transaction1 = new Transaction();
        //                 $transaction1->sender_user_id = $highestPricedCommentUser->id;
        //                 $transaction1->receiver_user_id = $produtOffre->id;
        //                 $transaction1->type = 'Envoie';
        //                 $transaction1->amount = $highestPricedComment->prixTrade;
        //                 $transaction1->save();

        //                 $transaction2 = new Transaction();
        //                 $transaction2->sender_user_id = $highestPricedCommentUser->id;
        //                 $transaction2->receiver_user_id = $produtOffre->id;
        //                 $transaction2->type = 'Reception';
        //                 $transaction2->amount = $highestPricedComment->prixTrade;
        //                 $transaction2->save();

        //                 Notification::send($highestPricedCommentUser, new NegosTerminer([
        //                     'message' => 'Félicitations! Vous avez fait le commentaire avec le prix le plus élevé avec ' . $highestPricedComment->prixTrade . '.',
        //                     'produit_id' => $produtOffre->id
        //                 ]));
        //                 Log::info('Notification sent to highest priced comment user', ['user' => $highestPricedCommentUser]);

        //                 Notification::send($produtOffre->user, new NegosTerminer([
        //                     'message' => 'Le commentaire avec le prix le plus élevé a été fait par: ' . $highestPricedCommentUserName,
        //                     'produit_id' => $produtOffre->id
        //                 ]));
        //                 Log::info('Notification sent to product owner', ['user' => $produtOffre->user]);

        //                 NotificationLog::create(['code_unique' => $codeUnique]);
        //                 Log::info('Notification log created', ['code_unique' => $codeUnique]);
        //             } else {
        //                 if (!$ownerWallet) {
        //                     Log::error('Portefeuille non trouvé pour l\'utilisateur ID: ' . $highestPricedCommentUser->id);
        //                 }
        //                 if (!$traderWallet) {
        //                     Log::error('Portefeuille non trouvé pour le trader ID: ' . $produtOffre->id);
        //                 }
        //             }
        //         } else {
        //             Log::error('Highest priced comment not found for code unique: ' . $codeUnique);
        //         }
        //     }
        // } elseif ($notification->type === 'App\Notifications\OffreNegosNotif') {
        //     $prixArticleNegos = null;
        //     $uniqueCode = $notification->data['code_unique'];

        //     $offreGroupeExistante = OffreGroupe::where('code_unique', $uniqueCode)->first();

        //     $differance = $offreGroupeExistante->differance;

        //     $notificationsNegos = DatabaseNotification::where('type', 'App\Notifications\OffreNegosNotif')
        //         ->where(function ($query) use ($uniqueCode) {
        //             $query->where('data->code_unique', $uniqueCode);
        //         })
        //         ->get();

        //     $oldestNotificationDate = $notificationsNegos->min('created_at');

        //     $tempsEcoule = $oldestNotificationDate ? Carbon::parse($oldestNotificationDate)->addMinutes(1) : null;

        //     // Vérifier si $tempsEcoule est écoulé
        //     $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

        //     $sommeQuantites = OffreGroupe::where('code_unique', $uniqueCode)
        //         ->sum('quantite');

        //     $nombreParticp = OffreGroupe::where('code_unique', $uniqueCode)
        //         ->distinct('user_id')
        //         ->count();
        //     $produit = ProduitService::find($notification->data['produit_id']);

        //     $notificationExists = NotificationLog::where('code_unique', $uniqueCode)->exists();

        //     if ($isTempsEcoule && !$notificationExists) {
        //         $data = [
        //             'quantite' => $sommeQuantites,
        //             'produit_id' => $notification->data['produit_id'],
        //             'produit_name' => $notification->data['produit_name'],
        //             'code_unique' => $uniqueCode
        //         ];
        //         if ($produit) {
        //             // Récupérer le user_id du produit
        //             $user_id = $produit->user_id;

        //             // Utiliser $user_id comme nécessaire
        //         } else {
        //             // Gérer le cas où le produit n'est pas trouvé
        //             Log::error('Produit non trouvé pour l\'ID: ' . $notification->data['produit_id']);
        //             return redirect()->back()->with('error', 'Produit non trouvé pour l\'ID spécifié.');
        //         }

        //         $idsProprietaires = Consommation::where('name', $notification->data['produit_name'])
        //             ->where('id_user', '!=', $produit->user_id)
        //             ->where('statuts', 'Accepté')
        //             ->distinct()
        //             ->pluck('id_user')
        //             ->toArray();

        //         // Recherchez le produit associé à l'ID de produit

        //         if ($differance) {

        //             foreach ($idsProprietaires as $conso) {
        //                 $owner = User::find($conso);

        //                 if ($owner) {
        //                     Notification::send($owner, new AppelOffre(['quantity' => $sommeQuantites, 'productName' => $notification->data['produit_name'], 'prodUsers' => $user_id]));
        //                 } else {
        //                     Log::error('Utilisateur non trouvé pour l\'ID: ' . $conso);
        //                 }
        //             }
        //         } else {

        //             foreach ($idsProprietaires as $conso) {
        //                 $owner = User::find($conso);

        //                 if ($owner) {
        //                     Notification::send($owner, new OffreNegosDone($data));
        //                 } else {
        //                     Log::error('Utilisateur non trouvé pour l\'ID: ' . $conso);
        //                 }
        //             }
        //         }
        //         NotificationLog::create(['code_unique' => $uniqueCode]);
        //     }
        // } elseif ($notification->type === 'App\Notifications\OffreNegosDone') {
        //     $produit = ProduitService::find($notification->data['produit_id']);

        //     $prixArticleNegos = $notification->data['quantite'] * $produit->prix;
        // }
        //elseif ($notification->type === 'App\Notifications\livraisonVerif') {
        //     $produit = ProduitService::find($notification->data['id_prod']);

        //     $userFour = User::find($notification->data['id_trader']);
        // }

        return view('livewire.notification-show');

        // return view('livewire.notification-show', compact(
        //     'notification',
        //     'produtOffre',
        //     'comments',
        //     'commentCount',
        //     'userComment',
        //     'oldestCommentDate',
        //     'isTempsEcoule',
        //     'codeUnique',
        //     'oldestNotificationDate',
        //     'sommeQuantites',
        //     'nombreParticp',
        //     'produit',
        //     'prixArticleNegos',
        //     'lowPriceUserName',
        //     'lowPriceAmount',
        //     'tempsEcoule',
        //     'highestPricedComment',
        //     'user',
        //     'nombreLivr',
        //     'userFour',
        // ));
    }
}
