<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InformationNego extends Component
{
    public $offreIniatiale;
    public $prixLePlusBas;
    public $id;

    /**
     * Create a new component instance.
     */
    public function __construct($offreIniatiale = null, $prixLePlusBas = null, $id)
    {
        $this->offreIniatiale = $offreIniatiale;
        $this->prixLePlusBas = $prixLePlusBas;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.information-nego');
    }
}
