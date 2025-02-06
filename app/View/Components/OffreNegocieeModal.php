<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\User; // Ensure the correct namespace for User model

class OffreNegocieeModal extends Component
{
    public $produit;
    public $nombreProprietaires;
    protected $users;

    public function __construct($produit, $nombreProprietaires, User $users)
    {
        $this->produit = $produit;
        $this->nombreProprietaires = $nombreProprietaires;
        $this->users = $users;
    }

    public function render()
    {
        return view('components.offre-negociee-modal');
    }
}
