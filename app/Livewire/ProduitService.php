<?php

namespace App\Livewire;

use App\Models\ProduitService as ModelsProduitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProduitService extends Component
{
    protected $layout = 'components.layouts.app';

    public function render()
    {
        // Récupérer l'utilisateur connecté via le gardien web
        $user = Auth::guard('web')->user();

        // Vérifier si l'utilisateur est authentifié
        if ($user) {
            // Récupérer les produits associés à cet utilisateur
            $produits = ModelsProduitService::where('user_id', $user->id)->orderBy('created_at', 'desc')
                ->paginate(10);

            // Compter le nombre de produits
            $prodCount = $produits->count();

            // Passer les produits à la vue
            return view('livewire.produit-service', ['produits' => $produits, 'prodCount' => $prodCount]);
        }
    }
}
