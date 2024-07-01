<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Comment;
use App\Models\AchatDirect;
use App\Models\OffreGroupe;
use App\Models\Transaction;
use App\Models\Consommation;
use App\Models\ProduitService;
use Illuminate\Support\Carbon;
use App\Models\NotificationLog;
use App\Notifications\AppelOffre;
use Illuminate\Support\Facades\Log;
use App\Notifications\NegosTerminer;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OffreNegosDone;
use App\Notifications\AppelOffreTerminer;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{

    public function index()
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Récupérer les notifications de l'utilisateur
        $notifications = $user->notifications;

        $unreadCount = $user->unreadNotifications->count();

        return view('biicf.notif', compact('notifications', 'unreadCount'));
    }
    public function show($id)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            // Récupérer la notification
            $notification = DatabaseNotification::findOrFail($id);

            // Marquer la notification comme lue
            // if ($notification->unread()) {
            //     $notification->markAsRead();
            // }

            // Initialiser la variable produit à null
            $produtOffre = null;
            $notificationExists = null;
            $oldestNotificationDate = null;
            $sommeQuantites = null;
            $nombreParticp = null;
            $produit = null;
            $prixArticleNegos = null;
            $lowPriceComment = null;
            $lowPriceUserName = null;
            $lowPriceAmount = null;
            $highestPricedComment = null;

            // Vérifier si 'produit_id' existe dans les données de notification
            if (isset($notification->data['produit_id'])) {
                // Récupérer le produit associé à la notification
                $produtOffre = ProduitService::find($notification->data['produit_id']);
            }

            // Vérifier si 'code_unique' existe dans les données de notification
            $codeUnique = $notification->data['code_unique'] ?? $notification->data['Uniquecode'] ?? null;


            // Récupérer les commentaires avec code_unique et prixTrade non nul
            $comments = Comment::with('user')
                ->where('code_unique', $codeUnique)
                ->whereNotNull('prixTrade')
                ->orderBy('prixTrade', 'asc')
                ->get();

            // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
            $oldestComment = Comment::where('code_unique', $codeUnique)
                ->whereNotNull('prixTrade')
                ->orderBy('created_at', 'asc')
                ->first();

            // Initialiser la variable pour la date du plus ancien commentaire
            $oldestCommentDate = $oldestComment ? $oldestComment->created_at : null;

            // Ajouter 5 heures à la date la plus ancienne, s'il y en a une
            $tempsEcoule = $oldestCommentDate ? Carbon::parse($oldestCommentDate)->addMinutes(1) : null;

            // Vérifier si $tempsEcoule est écoulé
            $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

            // Récupérer le commentaire de l'utilisateur connecté
            $userComment = Comment::with('user')
                ->where('code_unique', $codeUnique)
                ->where('id_trader', $user->id)
                ->first();

            // Compter le nombre de commentaires
            $commentCount = $comments->count();

            // Vérifier si le temps est écoulé /////\\\///////

            if ($notification->type === 'App\Notifications\AppelOffre') {
                Log::info('Notification type is App\Notifications\AppelOffre');

                $notificationExists = NotificationLog::where('code_unique', $codeUnique)->exists();
                $lowPriceComment = Comment::where('code_unique', $codeUnique)
                    ->whereNotNull('prixTrade')
                    ->orderBy('prixTrade', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lowPriceComment) {
                    Log::info('Low price comment found', ['lowPriceComment' => $lowPriceComment]);

                    $data = [
                        'prix_trade' => $lowPriceComment->prixTrade ?? null,
                        'id_trader' => $lowPriceComment->id_trader ?? null,
                        'id_prod' => $lowPriceComment->id_prod ?? null,
                        'quantite' => $notification->data['quantity'] ?? null,
                        'name' => $notification->data['productName'] ?? 'Produit sans nom'
                    ];
                    $lowPriceUserName = $lowPriceComment->user->name;
                    $lowPriceAmount = $lowPriceComment->prixTrade;

                    if ($isTempsEcoule && !$notificationExists) {
                        Log::info('Time has elapsed and no notification exists', ['code_unique' => $codeUnique]);

                        // Vérifier si 'id_sender' est un tableau ou un seul élément
                        $idSenders = is_array($notification->data['id_sender']) ? $notification->data['id_sender'] : [$notification->data['id_sender']];

                        foreach ($idSenders as $userSender) {
                            $owner = User::find($userSender);
                            Log::info('Processing user sender', ['userSender' => $userSender, 'owner' => $owner]);

                            if ($owner && $data['prix_trade'] && $data['quantite']) {
                                $prixArticle = $data['quantite'] * $data['prix_trade'];
                                Log::info('Price article calculated', ['prixArticle' => $prixArticle]);

                                // Trouver les portefeuilles du propriétaire et du trader
                                $ownerWallet = Wallet::where('user_id', $owner->id)->first();
                                $traderWallet = Wallet::where('user_id', $data['id_trader'])->first();
                                Log::info('Wallets found', ['ownerWallet' => $ownerWallet, 'traderWallet' => $traderWallet]);

                                if ($ownerWallet && $traderWallet) {
                                    // Décrémenter le portefeuille du trader
                                    $traderWallet->decrement('balance', $prixArticle);

                                    // Incrémenter le portefeuille du propriétaire
                                    $ownerWallet->increment('balance', $prixArticle);

                                    // Enregistrer la transaction d'envoi
                                    $transaction1 = new Transaction();
                                    $transaction1->sender_user_id = $owner->id;
                                    $transaction1->receiver_user_id = $data['id_trader'];
                                    $transaction1->type = 'Envoie';
                                    $transaction1->amount = $prixArticle;
                                    $transaction1->save();

                                    // Enregistrer la transaction de réception
                                    $transaction2 = new Transaction();
                                    $transaction2->sender_user_id = $owner->id;
                                    $transaction2->receiver_user_id = $data['id_trader'];
                                    $transaction2->type = 'Reception';
                                    $transaction2->amount = $prixArticle;
                                    $transaction2->save();

                                    // Envoyer la notification à l'utilisateur authentifié
                                    Notification::send($owner, new AppelOffreTerminer($data));
                                    Log::info('Notification sent', ['user' => $owner, 'data' => $data]);

                                    NotificationLog::create(['code_unique' => $codeUnique]);
                                } else {
                                    // Gérer le cas où le portefeuille du propriétaire ou du trader n'est pas trouvé
                                    if (!$ownerWallet) {
                                        Log::error('Portefeuille non trouvé pour l\'utilisateur ID: ' . $owner->id);
                                    }
                                    if (!$traderWallet) {
                                        Log::error('Portefeuille non trouvé pour le trader ID: ' . $data['id_trader']);
                                    }
                                }
                            } else {
                                // Gérer le cas où le propriétaire ou les données requises sont manquants
                                Log::error('Propriétaire non trouvé ou données manquantes pour userSender ID: ' . $userSender);
                            }
                        }
                    }
                } else {
                    $lowPriceUserName = 'N/A';
                    $lowPriceAmount = 0;
                }
            }
            elseif ($notification->type === 'App\Notifications\OffreNotifGroup') {
                Log::info('Notification type is OffreNotifGroup');

                $comments = Comment::with('user')
                    ->where('code_unique', $codeUnique)
                    ->whereNotNull('prixTrade')
                    ->orderBy('prixTrade', 'desc')
                    ->get();
                $oldestComment = Comment::where('code_unique', $codeUnique)
                    ->whereNotNull('prixTrade')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $oldestCommentDate = $oldestComment ? $oldestComment->created_at : null;
                $tempsEcoule = $oldestCommentDate ? Carbon::parse($oldestCommentDate)->addMinutes(1) : null;
                $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

                Log::info('Oldest comment date', ['oldestCommentDate' => $oldestCommentDate]);
                Log::info('Temps écoulé', ['tempsEcoule' => $tempsEcoule, 'isTempsEcoule' => $isTempsEcoule]);

                $notificationExists = NotificationLog::where('code_unique', $codeUnique)->exists();
                Log::info('Notification exists', ['exists' => $notificationExists]);

                if ($isTempsEcoule && !$notificationExists) {
                    Log::info('Time has elapsed and no notification exists', ['code_unique' => $codeUnique]);

                    $highestPricedComment = Comment::where('code_unique', $codeUnique)
                        ->whereNotNull('prixTrade')
                        ->orderBy('prixTrade', 'desc')
                        ->orderBy('created_at', 'asc')
                        ->first();
                    Log::info('Highest priced comment', ['highestPricedComment' => $highestPricedComment]);

                    if ($highestPricedComment) {
                        $highestPricedCommentUser = $highestPricedComment->user;
                        $highestPricedCommentUserName = $highestPricedCommentUser->name;
                        Log::info('Highest priced comment user', ['user' => $highestPricedCommentUser]);

                        $ownerWallet = Wallet::where('user_id', $highestPricedCommentUser->id)->first();
                        $traderWallet = Wallet::where('user_id', $produtOffre->id)->first();
                        Log::info('Wallets found', ['ownerWallet' => $ownerWallet, 'traderWallet' => $traderWallet]);

                        if ($ownerWallet && $traderWallet) {
                            $ownerWallet->decrement('balance', $highestPricedComment->prixTrade);
                            $traderWallet->increment('balance', $highestPricedComment->prixTrade);

                            $transaction1 = new Transaction();
                            $transaction1->sender_user_id = $highestPricedCommentUser->id;
                            $transaction1->receiver_user_id = $produtOffre->id;
                            $transaction1->type = 'Envoie';
                            $transaction1->amount = $highestPricedComment->prixTrade;
                            $transaction1->save();

                            $transaction2 = new Transaction();
                            $transaction2->sender_user_id = $highestPricedCommentUser->id;
                            $transaction2->receiver_user_id = $produtOffre->id;
                            $transaction2->type = 'Reception';
                            $transaction2->amount = $highestPricedComment->prixTrade;
                            $transaction2->save();

                            Notification::send($highestPricedCommentUser, new NegosTerminer([
                                'message' => 'Félicitations! Vous avez fait le commentaire avec le prix le plus élevé avec '. $highestPricedComment->prixTrade .'.',
                                'produit_id' => $produtOffre->id
                            ]));
                            Log::info('Notification sent to highest priced comment user', ['user' => $highestPricedCommentUser]);

                            Notification::send($produtOffre->user, new NegosTerminer([
                                'message' => 'Le commentaire avec le prix le plus élevé a été fait par: ' . $highestPricedCommentUserName,
                                'produit_id' => $produtOffre->id
                            ]));
                            Log::info('Notification sent to product owner', ['user' => $produtOffre->user]);

                            NotificationLog::create(['code_unique' => $codeUnique]);
                            Log::info('Notification log created', ['code_unique' => $codeUnique]);
                        } else {
                            if (!$ownerWallet) {
                                Log::error('Portefeuille non trouvé pour l\'utilisateur ID: ' . $highestPricedCommentUser->id);
                            }
                            if (!$traderWallet) {
                                Log::error('Portefeuille non trouvé pour le trader ID: ' . $produtOffre->id);
                            }
                        }
                    } else {
                        Log::error('Highest priced comment not found for code unique: ' . $codeUnique);
                    }
                }
            }elseif ($notification->type === 'App\Notifications\OffreNegosNotif') {
                $prixArticleNegos = null;
                $uniqueCode = $notification->data['code_unique'];

                $offreGroupeExistante = OffreGroupe::where('code_unique', $uniqueCode)->first();

                $differance = $offreGroupeExistante->differance;

                $notificationsNegos = DatabaseNotification::where('type', 'App\Notifications\OffreNegosNotif')
                    ->where(function ($query) use ($uniqueCode) {
                        $query->where('data->code_unique', $uniqueCode);
                    })
                    ->get();

                $oldestNotificationDate = $notificationsNegos->min('created_at');

                $tempsEcoule = $oldestNotificationDate ? Carbon::parse($oldestNotificationDate)->addMinutes(1) : null;

                // Vérifier si $tempsEcoule est écoulé
                $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

                $sommeQuantites = OffreGroupe::where('code_unique', $uniqueCode)
                    ->sum('quantite');

                $nombreParticp = OffreGroupe::where('code_unique', $uniqueCode)
                    ->distinct('user_id')
                    ->count();
                $produit = ProduitService::find($notification->data['produit_id']);

                $notificationExists = NotificationLog::where('code_unique', $uniqueCode)->exists();

                if ($isTempsEcoule && !$notificationExists) {
                    $data = [
                        'quantite' => $sommeQuantites,
                        'produit_id' => $notification->data['produit_id'],
                        'produit_name' => $notification->data['produit_name'],
                        'code_unique' => $uniqueCode
                    ];
                    if ($produit) {
                        // Récupérer le user_id du produit
                        $user_id = $produit->user_id;

                        // Utiliser $user_id comme nécessaire
                    } else {
                        // Gérer le cas où le produit n'est pas trouvé
                        Log::error('Produit non trouvé pour l\'ID: ' . $notification->data['produit_id']);
                        return redirect()->back()->with('error', 'Produit non trouvé pour l\'ID spécifié.');
                    }

                    $idsProprietaires = Consommation::where('name', $notification->data['produit_name'])
                        ->where('id_user', '!=', $produit->user_id)
                        ->where('statuts', 'Accepté')
                        ->distinct()
                        ->pluck('id_user')
                        ->toArray();

                    // Recherchez le produit associé à l'ID de produit

                    if ($differance) {

                        foreach ($idsProprietaires as $conso) {
                            $owner = User::find($conso);

                            if ($owner) {
                                Notification::send($owner, new AppelOffre(['quantity' => $sommeQuantites, 'productName' => $notification->data['produit_name'], 'prodUsers' => $user_id]));
                            } else {
                                Log::error('Utilisateur non trouvé pour l\'ID: ' . $conso);
                            }
                        }
                    } else {

                        foreach ($idsProprietaires as $conso) {
                            $owner = User::find($conso);

                            if ($owner) {
                                Notification::send($owner, new OffreNegosDone($data));
                            } else {
                                Log::error('Utilisateur non trouvé pour l\'ID: ' . $conso);
                            }
                        }
                    }
                    NotificationLog::create(['code_unique' => $uniqueCode]);
                }
            }elseif ($notification->type === 'App\Notifications\OffreNegosDone') {
                $produit = ProduitService::find($notification->data['produit_id']);

                $prixArticleNegos = $notification->data['quantite'] * $produit->prix;
            }return view('biicf.notifshow', compact(
                'notification',
                'produtOffre',
                'comments',
                'commentCount',
                'userComment',
                'oldestCommentDate',
                'isTempsEcoule',
                'codeUnique',
                'oldestNotificationDate',
                'sommeQuantites',
                'nombreParticp',
                'produit',
                'prixArticleNegos',
                'lowPriceUserName',
                'lowPriceAmount',
                'tempsEcoule',
                'highestPricedComment'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
        }
    }
}
