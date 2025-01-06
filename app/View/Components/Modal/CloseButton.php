<?php

namespace App\View\Components\Modal;

use Illuminate\View\Component;

class CloseButton extends Component
{
    public $modalId;

    public function __construct($modalId)
    {
        $this->modalId = $modalId;
    }

    public function render()
    {
        return view('components.modal.close-button');
    }
}
