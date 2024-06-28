<?php

namespace App\Livewire;

use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicationProduits extends Component
{
    public $search = '';

    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function render()
    {
        $produits = ProduitService::with('user')
            ->where('type', 'produits')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $produits = ProduitService::latest()
            ->where('name', 'like', "%{$this->search}%")
            ->paginate(5);
            
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


        return view('livewire.publication-produits', [
            'produits' => $produits,
            'adminId' => $adminId,
            'produitAgents' => $produitAgents,
            'produitAgentsCount' => $produitAgentsCount,
            'prodCount' => $prodCount

        ]);
    }
}
