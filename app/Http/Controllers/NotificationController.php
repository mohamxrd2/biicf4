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

        // Ajouter 5 heures à la date la plus ancienne, s'il y en a une
        $tempsEcoule = $oldestCommentDate ? Carbon::parse($oldestCommentDate)->addHours(5) : null;

        // Vérifier si $tempsEcoule est écoulé
        $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

        // Récupérer le commentaire de l'utilisateur connecté
        $userComment = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->where('id_trader', $user->id)
            ->first();

        // Compter le nombre de commentaires
        $commentCount = $comments->count();

        // Vérifier si le temps est écoulé
        if ($isTempsEcoule) {
            
        }

        return view('biicf.notifshow', compact('notification', 'produtOffre', 'comments', 'commentCount', 'userComment', 'oldestCommentDate', 'tempsEcoule'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
    }
}
}
