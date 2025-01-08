<?php

namespace App\View\Components;

use Illuminate\View\Component;

class OffreNegocieeModal extends Component
{
    public $produit;
    public $nombreProprietaires;
    public $users;

    public function __construct($produit, $nombreProprietaires, $users)
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
