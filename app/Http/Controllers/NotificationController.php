<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\AchatDirect;
use App\Models\ProduitService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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

            // Vérifier si 'produit_id' existe dans les données de notification
            if (isset($notification->data['produit_id'])) {
                // Récupérer le produit associé à la notification
                $produtOffre = ProduitService::find($notification->data['produit_id']);
            }

            $codeUnique = $notification->data['code_unique'];


            $comments = Comment::with('user')
                ->where('code_unique', $codeUnique)
                ->whereNotNull('prixTrade')
                ->orderBy('prixTrade', 'asc')
                ->get();

            $userComment = Comment::with('user')
                ->where('code_unique', $codeUnique)
                ->where('id_trader', $user->id)
                ->first();

            $oldestComment = Comment::where('code_unique', $codeUnique)
                ->whereNotNull('prixTrade')
                ->orderBy('created_at', 'asc')
                ->first();

            $commentCount = $comments->count();

            $oldestCommentDate = $oldestComment->created_at;

            // Ajouter 5 heures à la date la plus ancienne
            $tempsEcoule = Carbon::parse($oldestCommentDate)->addHours(5);
            // $tempEcoule = Carbon::now()->subDays(1); // pour le test

            if (Carbon::now()->greaterThan($tempsEcoule)) {
                $comment = Comment::where('code_unique', $codeUnique)
                    ->whereNotNull('prixTrade')
                    ->orderBy('prixTrade', 'asc') // Assurez-vous qu'il est trié par prixTrade croissant
                    ->first(); // Utilisez first() pour obtenir seulement le premier résultat avec le prix le plus bas

                // Vérifiez si un commentaire avec un prixTrade non nul a été trouvé
                if ($comment) {
                    // Maintenant, $comment contient le commentaire avec le prixTrade le plus bas pour le codeUnique spécifié
                    // Vous pouvez accéder aux propriétés du commentaire comme ceci :
                    $prixTradePlusBas = $comment->prixTrade;
                    $idTrader = $comment->id_trader;

                    // Vous pouvez également récupérer l'utilisateur associé si nécessaire
                    $user = $comment->user;

                    
                } else {
                    // Gérer le cas où aucun commentaire avec un prixTrade non nul n'a été trouvé pour le codeUnique spécifié
                    
                }
            }




            return view('biicf.notifshow', compact('notification', 'produtOffre', 'comments', 'commentCount', 'userComment', 'oldestCommentDate'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
        }
    }
}
