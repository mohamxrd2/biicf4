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
            $oldestNotificationDate = null;
            $sommeQuantites = null;
            $nombreParticp = null;
            $produit = null;
            $prixArticleNegos = null;

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

                if ($isTempsEcoule) {
                    // Récupérer le commentaire avec le prix le plus bas
                    $lowPriceComment = Comment::where('code_unique', $codeUnique)
                        ->whereNotNull('prixTrade')
                        ->orderBy('prixTrade', 'asc')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($lowPriceComment) {
                        $data = [
                            'prix_trade' => $lowPriceComment->prixTrade ?? null,
                            'id_trader' => $lowPriceComment->id_trader ?? null,
                            'id_prod' => $lowPriceComment->id_prod ?? null,
                            'quantite' => $notification->data['quantity'] ?? null,
                            'name' => $notification->data['productName'] ?? 'Produit sans nom'
                        ];

                        // Celui qui a fait l'appel de l'offre
                        $owner = User::find($notification->data['id_sender']);

                        if ($owner && $data['prix_trade'] && $data['quantite']) {

                            $prixArticle = $data['quantite'] * $data['prix_trade'];

                            // Trouver les portefeuilles du propriétaire et du trader
                            $ownerWallet = Wallet::where('user_id', $owner->id)->first();
                            $traderWallet = Wallet::where('user_id', $data['id_trader'])->first();

                            if ($ownerWallet && $traderWallet) {
                                // Décrémenter le portefeuille du trader
                                $traderWallet->decrement('balance', $prixArticle);

                                // Incrémenter le portefeuille du propriétaire
                                $ownerWallet->increment('balance', $prixArticle);

                                // Enregistrer la transaction de envoi
                                $transaction1 = new Transaction();
                                $transaction1->sender_user_id = $owner->id;
                                $transaction1->receiver_user_id = $data['id_trader'];
                                $transaction1->type = 'Envoie';
                                $transaction1->amount = $prixArticle;
                                $transaction1->save();

                                // Enregistrer la transaction reception
                                $transaction2 = new Transaction();
                                $transaction2->sender_user_id = $owner->id;
                                $transaction2->receiver_user_id = $data['id_trader'];
                                $transaction2->type = 'Reception';
                                $transaction2->amount = $prixArticle;
                                $transaction2->save();

                                // Envoyer la notification à l'utilisateur authentifié
                                Notification::send($owner, new AppelOffreTerminer($data));
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
                            Log::error('Propriétaire non trouvé ou données manquantes');
                        }
                    }
                }
            } elseif ($notification->type === 'App\Notifications\OffreNotifGroup') {

                $comments = Comment::with('user')
                ->where('code_unique', $codeUnique)
                ->whereNotNull('prixTrade')
                ->orderBy('prixTrade', 'desc')
                ->get();

                if ($isTempsEcoule) {
                    // Récupérer le commentaire avec le prix le plus élevé, en cas d'égalité prendre le plus ancien
                    $highestPricedComment = Comment::where('code_unique', $codeUnique)
                        ->whereNotNull('prixTrade')
                        ->orderBy('prixTrade', 'desc')
                        ->orderBy('created_at', 'asc')
                        ->first();

                    // Récupérer l'utilisateur ayant fait ce commentaire
                    if ($highestPricedComment) {
                        $highestPricedCommentUser = $highestPricedComment->user;
                        $highestPricedCommentUserName = $highestPricedCommentUser->name;

                        // Envoyer la notification à cet utilisateur
                        Notification::send($highestPricedCommentUser, new NegosTerminer([
                            'message' => 'Félicitations! Vous avez fait le commentaire avec le prix le plus élevé.',
                            'produit_id' => $produtOffre->id
                        ]));

                        // Envoyer la notification au propriétaire du produit
                        Notification::send($produtOffre->user, new NegosTerminer([
                            'message' => 'Le commentaire avec le prix le plus élevé a été fait par: ' . $highestPricedCommentUserName,
                            'produit_id' => $produtOffre->id
                        ]));

                        // Enregistrer la notification dans la table NotificationLog
                        NotificationLog::create(['idProd' => $produtOffre->id]);
                    }
                }
            } elseif ($notification->type === 'App\Notifications\OffreNegosNotif') {
                $prixArticleNegos = null;
                $uniqueCode = $notification->data['code_unique'];

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

                if ($isTempsEcoule) {
                    $data = [
                        'quantite' => $sommeQuantites,
                        'produit_id' => $notification->data['produit_id'],
                        'produit_name' => $notification->data['produit_name']
                    ];

                    // Recherchez le produit associé à l'ID de produit


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

                    foreach ($idsProprietaires as $conso) {
                        $owner = User::find($conso);

                        if ($owner) {
                            Notification::send($owner, new OffreNegosDone($data));
                        } else {
                            Log::error('Utilisateur non trouvé pour l\'ID: ' . $conso);
                        }
                    }
                }
            } elseif ($notification->type === 'App\Notifications\OffreNegosDone') {
                $produit = ProduitService::find($notification->data['produit_id']);

                $prixArticleNegos = $notification->data['quantite'] * $produit->prix;
            }

            return view('biicf.notifshow', compact(
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
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
        }
    }
}
