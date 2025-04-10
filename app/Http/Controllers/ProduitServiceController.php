<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\AchatGrouper;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use App\Models\NotificationLog;
use App\Notifications\AchatBiicf;
use App\Http\Controllers\Controller;
use App\Models\Consommation;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AchatGroupBiicf;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon; // Import de la classe Carbon

class ProduitServiceController extends Controller
{
    //

    public function adminProduct()
    {

        return view('admin.products');
    }


    public function adminService()
    {

        return view('admin.services');
    }

    public function pubDet($id)
    {
        try {
            // // Récupérer le produit ou échouer
            $produit = ProduitService::findOrFail($id);

            // Récupérer l'identifiant de l'utilisateur connecté
            $userId = Auth::guard('web')->id();

            // // Récupérer le portefeuille de l'utilisateur
            $userWallet = Wallet::where('user_id', $userId)->first();

            // Retourner la vue avec les données récupérées
            return view('biicf.postdetail', compact(
                'produit',
                'userWallet',
                'userId',
                'id',
            ));
        } catch (\Exception $e) {
            // Gérer les exceptions et rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
}
