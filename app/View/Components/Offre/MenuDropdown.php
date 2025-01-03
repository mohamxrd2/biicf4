<?php

namespace App\View\Components\Offre;

use Illuminate\View\Component;

class MenuDropdown extends Component
{
    public $produit;

    public function __construct($produit)
    {
        $this->produit = $produit;
    }

    public function render()
    {
        return view('components.offre.menu-dropdown');
    }
}
