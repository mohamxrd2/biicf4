<?php

namespace App\Http\Controllers;

use App\Models\AchatDirect;

use App\Models\ProduitService;
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

        return view('biicf.notifshow', compact('notification', 'produtOffre'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
    }
}

}
