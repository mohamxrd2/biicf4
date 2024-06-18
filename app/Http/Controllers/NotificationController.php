<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Comment;
use App\Models\AchatDirect;
use App\Models\Transaction;
use App\Models\ProduitService;
use Illuminate\Support\Carbon;
use App\Notifications\NegosTerminer;
use Illuminate\Support\Facades\Auth;
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
            if ($notification->unread()) {
                $notification->markAsRead();
            }

            // Initialiser la variable produit à null
            $produtOffre = null;

          

            // Vérifier si 'produit_id' existe dans les données de notification
            if (isset($notification->data['produit_id'])) {
                // Récupérer le produit associé à la notification
                $produtOffre = ProduitService::find($notification->data['produit_id']);
            }

            // Vérifier si 'code_unique' existe dans les données de notification
            $codeUnique = $notification->data['code_unique'] ?? null;

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

            // Ajouter 5 minutes à la date la plus ancienne, s'il y en a une (pour les tests)
            $tempsEcoule = $oldestCommentDate ? Carbon::parse($oldestCommentDate)->addMinutes(5) : null;

            // Vérifier si $tempsEcoule est écoulé
            $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();


            //   // Initialiser la variable pour la date du plus ancien commentaire (supposons que c'était il y a plus de 6 heures)
            // $oldestCommentDate = now()->subHours(6); // Date d'il y a plus de 6 heures pour simuler que le temps est écoulé

            // // Vérifier si $tempsEcoule est écoulé
            // $isTempsEcoule = $oldestCommentDate->isPast();

            // Récupérer le commentaire de l'utilisateur connecté
            $userComment = Comment::with('user')
                ->where('code_unique', $codeUnique)
                ->where('id_trader', $user->id)
                ->first();

            // Compter le nombre de commentaires
            $commentCount = $comments->count();

            // Vérifier si le temps est écoulé
         if($notification->type === 'App\Notifications\NegosTerminer' ){

            if ($isTempsEcoule) {
                // Récupérer le commentaire avec le prix le plus bas
                $lowPriceComment = Comment::where('code_unique', $codeUnique)
                    ->whereNotNull('prixTrade')
                    ->orderBy('prixTrade', 'asc')
                    ->first();

                if ($lowPriceComment) {
                    $data = [
                        'prix_trade' => $lowPriceComment->prixTrade ?? null,
                        'id_trader' => $lowPriceComment->id_trader ?? null,
                        'id_prod' => $lowPriceComment->id_prod ?? null,
                        'quantite' => $notification->data['quantity'] ?? null,
                        'name' => $notification->data['productName'] ?? 'Produit sans nom'
                    ];


                    $owner = User::find($notification->data['id_sender']);
                    

                    $prixArticle = $notification->data['quantite'] * $notification->data['prix_trade'];


                    $ownerWallet = Wallet::where('user_id', $notification->data['id_sender'])->first();

                    $ownerWallet->increment('balance', $prixArticle);

                    $transaction1 = new Transaction();
                    $transaction1->sender_admin_id = $owner->id;
                    $transaction1->receiver_user_id = $lowPriceComment->id_trader;
                    $transaction1->type = 'Reception';
                    $transaction1->amount = $prixArticle;
                    $transaction1->save();

                    $transaction2 = new Transaction();
                    $transaction2->sender_admin_id = $owner->id;
                    $transaction2->receiver_user_id = $lowPriceComment->id_trader;
                    $transaction2->type = 'Envoie';
                    $transaction2->amount = $prixArticle;
                    $transaction2->save();


                    // Envoyer la notification à l'utilisateur authentifié
                    Notification::send($owner, new NegosTerminer($data));
                }
            }

         }
            

            return view('biicf.notifshow', compact('notification', 'produtOffre', 'comments', 'commentCount', 'userComment', 'oldestCommentDate', 'isTempsEcoule', ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
        }
    }
}
