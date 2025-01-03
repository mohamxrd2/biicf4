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


            return view('biicf.notifshow', compact(
                'notification',
                'id'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération de la notification: ' . $e->getMessage());
        }
    }
}
