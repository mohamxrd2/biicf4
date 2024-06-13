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




            return view('biicf.notifshow', compact('notification', 'produtOffre', 'comments', 'commentCount', 'userComment', 'oldestCommentDate'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
        }
    }
}
