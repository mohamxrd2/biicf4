<?php

namespace App\View\Components;

use Illuminate\View\Component;

class OffreGroupeeModal extends Component
{
    public $produit;
    public $nombreFournisseurs;

    public function __construct($produit, $nombreFournisseurs)
    {
        $this->produit = $produit;
        $this->nombreFournisseurs = $nombreFournisseurs;
    }

    public function render()
    {
        return view('components.offre-groupee-modal');
    }
}
