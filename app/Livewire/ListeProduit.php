<?php

namespace App\Livewire;

use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListeProduit extends Component
{
    use WithPagination;

    public $prodCount = '';
    public $produits = [];
    public $model; // Instance de votre modèle
    public $user ; // Instance de votre modèle
    public $image ; // Instance de votre modèle


    public function mount($user)
    {
        $this->user = Auth::guard('web')->user();

        if ($this->user) {
            $this->model = ProduitService::where('user_id', $this->user->id)->first();

            $this->refreshProducts();
        }
        $this->image = $this->model ? json_decode($this->model->images, true) : [];


    }

    #[On('form-submitted')]
    public function refreshProducts($produit = null)
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            // Récupérer les produits associés à cet utilisateur
            $produits  = ProduitService::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            $this->produits = $produits->items();
            // Compter le nombre de produits
            $this->prodCount = $produits->total(); // total() pour la pagination
        }
    }

    public function render()
    {

        return view('livewire.liste-produit');
    }
}
