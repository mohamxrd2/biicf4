<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    //
    public function index()
    {
        //Admin//////

        // Nombre total de clients
        $totalClients = User::count();

        // Nombre total de produits et de services
        $totalProducts = ProduitService::where('type', 'produits')->count();
        $totalServices = ProduitService::where('type', 'services')->count();

        // Liste des 5 derniers utilisateurs
        $users = User::orderBy('created_at', 'desc')->take(5)->get();

        $agents = Admin::where('admin_type', 'agent')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $agentCount = Admin::where('admin_type', 'agent')->count();

        //Agent//////

        //  l'agent connecté
        $adminId = Auth::guard('admin')->id();
        // Portefeuille de l'agent
        $adminWallet = Wallet::where('admin_id', $adminId)->first();
        // Récupérer les utilisateurs ayant le même admin_id que l'agent
        $usersWithSameAdminId = User::where('admin_id', $adminId)->get();
        // Nombre total d'utilisateurs ayant le même admin_id que l'agent
        $userCount = User::where('admin_id', $adminId)->count();
        // Nombre total d'éléments dans la table produits_service ayant le même admin_id pour le type de service

        $productsCount  = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'produits')
            ->count();

        $servicesCount  = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'services')
            ->count();


        return view('admin.dashboard', [
            'totalClients' => $totalClients,
            'totalProducts' => $totalProducts,
            'totalServices' => $totalServices,
            'users' => $users,
            'adminWallet' => $adminWallet,
            'userCount' => $userCount,
            'usersWithSameAdminId' => $usersWithSameAdminId,
            'servicesCount' => $servicesCount,
            'productsCount' => $productsCount,
            'agents' => $agents,
            'agentCount' => $agentCount 
        ]);
    }
}
