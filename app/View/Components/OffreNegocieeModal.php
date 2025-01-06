<?php

namespace App\View\Components;

use Illuminate\View\Component;

class OffreNegocieeModal extends Component
{
    public $produit;
    public $nombreProprietaires;

    public function __construct($produit, $nombreProprietaires)
    {
        $this->produit = $produit;
        $this->nombreProprietaires = $nombreProprietaires;
    }

    public function render()
    {
        return view('components.offre-negociee-modal');
    }
}
