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

        $produits = ProduitService::where('type', 'Produit')
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        // //Agent/////

        $prodCount = $produits->count();

        //  l'agent connecté
        $adminId = Auth::guard('admin')->id();
        // Récupérer les Produit avec l'utilisateur associé ayant le même admin_id
        $produitAgents = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'Produit')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        // Compter le nombre de produits qui correspondent aux critères spécifiés
        $produitAgentsCount = ProduitService::with('user')
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            })
            ->where('type', 'Produit')
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
