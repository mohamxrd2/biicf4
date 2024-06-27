<?php

namespace App\Livewire;

use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicationServices extends Component
{
    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function render()
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

        return view('livewire.publication-services', [
            'services' => $services,
            'adminId' => $adminId,
            'serviceAgents' => $serviceAgents,
            'serviceAgentsCount' => $serviceAgentsCount,
            'servieCount' => $servieCount
        ]);
    }
}
