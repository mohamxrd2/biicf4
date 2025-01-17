<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProduitServiceDetails extends Component
{
    public $id;
    public $produit;
    public $userWallet;
    public $userId;
    public $currentPage = 'produit';
    protected $listeners = ['navigate' => 'setPage'];
    public function setPage($page)
    {
        $this->currentPage = $page;
    }

    public function achat()
    {
        sleep(2); // Add a delay of 2 seconds
        $this->dispatch('navigate', 'achat');
    }
    public function credit()
    {
        $this->dispatch('navigate', 'credit');
    }

    public function mount($id)
    {
        $this->id = $id;
        try {
            $this->produit = ProduitService::findOrFail($id);
            $this->userId = Auth::guard('web')->id();
            $this->userWallet = Wallet::where('user_id', $this->userId)->first();
        } catch (\Exception $e) {
            session()->flash('error', 'Produit non trouvé');
            return redirect()->route('home');
        }
    }

    public function render()
    {
        try {
            return view('livewire.produit-service-details', [
                'produit' => $this->produit,
                'userWallet' => $this->userWallet,
                'userId' => $this->userId,
                'id' => $this->id,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
            return redirect()->route('home');
        }
    }
}
