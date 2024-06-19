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
        $produits = ProduitService::with('user')
            ->where('type', 'produits')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        //Agent/////

        $prodCount = $produits->count();

        //  l'agent connecté
        $adminId = Auth::guard('admin')->id();
        // Récupérer les produits avec l'utilisateur associé ayant le même admin_id
        $produitAgents = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'produits')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        // Compter le nombre de produits qui correspondent aux critères spécifiés
        $produitAgentsCount = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'produits')
            ->count();
        return view('admin.products', [
            'produits' => $produits,
            'adminId' => $adminId,
            'produitAgents' => $produitAgents,
            'produitAgentsCount' => $produitAgentsCount,
            'prodCount' => $prodCount

        ]);
    }

    public function destroyProduct($id)
    {
        $produit = ProduitService::find($id);

        if (!$produit) {
            return redirect()->back()->with('error', 'Produit non trouvé.');
        }

        $produit->delete();

        return redirect()->back()->with('success', 'Produit supprimé avec succès.');
    }
    public function destroyProductBiicf($id)
    {
        $produit = ProduitService::find($id);

        if (!$produit) {
            return redirect()->back()->with('error', 'Produit non trouvé.');
        }

        $produit->delete();

        return redirect()->route('biicf.post')->with('success', 'Produit supprimé avec succès.');
    }
    public function adminService()
    {

        $services = ProduitService::with('user')
            ->where('type', 'services')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        //Agent//////

        $servieCount = $services->count();

        //  l'agent connecté
        $adminId = Auth::guard('admin')->id();
        // affiche dans la table produits_service ayant le même admin_id pour le type de service
        $serviceAgents = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'services')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $serviceAgentsCount = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'services')
            ->count();

        return view('admin.services', [
            'services' => $services,
            'adminId' => $adminId,
            'serviceAgents' => $serviceAgents,
            'serviceAgentsCount' => $serviceAgentsCount,
            'servieCount' => $servieCount
        ]);
    }

    public function destroyService($id)
    {

        $services = ProduitService::find($id);

        if (!$services) {
            return redirect()->back()->with('error', 'Service non trouvé.');
        }

        $services->delete();

        return redirect()->route('admin.services')->with('success', 'Le service a été supprimé avec succès');
    }

    public function postBiicf()
    {
        // Récupérer l'utilisateur connecté via le gardien web
        $user = Auth::guard('web')->user();

        // Vérifier si l'utilisateur est authentifié
        if ($user) {
            // Récupérer les produits associés à cet utilisateur
            $produits = ProduitService::where('user_id', $user->id)->orderBy('created_at', 'desc')
                ->paginate(10);


            // Compter le nombre de produits
            $prodCount = $produits->count();

            // Passer les produits à la vue
            return view('biicf.post', ['produits' => $produits, 'prodCount' => $prodCount]);
        } else {
            // Rediriger l'utilisateur vers la page de connexion s'il n'est pas authentifié
            return redirect()->route('login');
        }
    }

    public function homeBiicf()
    {

        $produits = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $users = User::orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('biicf.acceuil', compact('users', 'produits',));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $zoneEconomique = $request->input('zone_economique');
        $type = $request->input('type');

        // Faire la recherche dans la base de données en fonction des filtres
        $produits = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->orderBy('created_at', 'desc');


        if ($keyword) {
            $produits->where('name', 'like', '%' . $keyword . '%');
        }

        if ($zoneEconomique) {
            $produits->where('zonecoServ', $zoneEconomique);
        }

        if ($type) {
            $produits->where('type', $type);
        }

        $results = $produits->paginate(10);

        $resultCount = $results->total();

        $produitDims = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('biicf.search', compact('results', 'produits', 'resultCount', 'produitDims'));
    }

    public function pubDet($id)
    {
        $userId = Auth::guard('web')->id();

        $produit = ProduitService::findOrFail($id);
        $nomProduit = $produit->name;

        // 2. Requête pour récupérer les IDs des propriétaires des consommations similaires
        $idsProprietaires = Consommation::where('name', $nomProduit)
            ->where('id_user', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // 3. Compter le nombre d'IDs distincts
        $nombreProprietaires = count($idsProprietaires);
        try {
            // Trouver le produit ou échouer
            $produit = ProduitService::findOrFail($id);

            // Récupérer l'identifiant de l'utilisateur connecté


            // Récupérer le portefeuille de l'utilisateur
            $userWallet = Wallet::where('user_id', $userId)->first();

            // Récupérer le nombre d'achats groupés distincts pour ce produit
            $nbreAchatGroup = AchatGrouper::where('idProd', $produit->id)
                ->distinct('userSender')
                ->count('userSender');

            // Récupérer la date la plus ancienne parmi les achats groupés pour ce produit
            $datePlusAncienne = AchatGrouper::where('idProd', $produit->id)->min('created_at');
            $tempEcoule = Carbon::now()->addDays(5);


            // Initialiser les variables pour la vue
            $sommeQuantite = AchatGrouper::where('idProd', $produit->id)->sum('quantité');
            $montants = AchatGrouper::where('idProd', $produit->id)->sum('montantTotal');
            $nameProd = $produit->name;
            $photoProd = $produit->photoProd1;
            $userSenders = AchatGrouper::where('idProd', $produit->id)
                ->distinct('userSender')
                ->pluck('userSender')
                ->toArray();

            // Vérifier si une notification a déjà été envoyée pour ce produit
            $notificationExists = NotificationLog::where('idProd', $produit->id)->exists();

            if (Carbon::now()->greaterThan($tempEcoule) && !$notificationExists && $nbreAchatGroup) {
                // Préparer le tableau de données pour la notification
                $notificationData = [
                    'nameProd' => $nameProd,
                    'quantité' => $sommeQuantite,
                    'montantTotal' => $montants,
                    'userTrader' => $produit->user->id,
                    'photoProd' => $photoProd,
                    'idProd' => $produit->id,
                    'userSender' => $userSenders
                ];

                // Envoyer la notification
                Notification::send($produit->user, new AchatGroupBiicf($notificationData));

                // Enregistrer la notification dans la table NotificationLog
                NotificationLog::create(['idProd' => $produit->id]);

                // Supprimer toutes les lignes dans AchatGrouper pour ce produit
                AchatGrouper::where('idProd', $produit->id)->delete();
            }

            // Retourner la vue avec les données récupérées
            return view('biicf.postdetail', compact('produit', 'userWallet', 'userId', 'id', 'nbreAchatGroup', 'datePlusAncienne', 'tempEcoule', 'sommeQuantite', 'montants', 'userSenders', 'idsProprietaires', 'nombreProprietaires'));
        } catch (\Exception $e) {
            // Gérer les exceptions et rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
}
