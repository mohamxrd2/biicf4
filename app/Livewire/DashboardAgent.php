<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardAgent extends Component
{
    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function render()
    {
        sleep(1);

        //  l'agent connecté
        $adminId = Auth::guard('admin')->id();

        // Portefeuille de l'agent
        $adminWallet = Wallet::where('admin_id', $adminId)->first();

        // Récupérer les utilisateurs ayant le même admin_id que l'agent
        $usersWithSameAdminId = User::where('admin_id', $adminId)->get();

        // Nombre total d'utilisateurs ayant le même admin_id que l'agent
        $userCount = User::where('admin_id', $adminId)->count();

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
        return view('livewire.dashboard-agent', [
            'adminWallet' => $adminWallet,
            'userCount' => $userCount,
            'usersWithSameAdminId' => $usersWithSameAdminId,
            'productsCount' => $productsCount,
            'servicesCount' => $servicesCount,
        ]);
    }
}
