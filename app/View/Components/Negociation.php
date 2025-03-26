<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Negociation extends Component
{
    public $comments;
    public $achatdirect;
    public $successMessage;
    public $errorMessage;

    /**
     * Create a new component instance.
     */
    public function __construct($comments, $achatdirect, $successMessage, $errorMessage)
    {
        $this->comments = $comments;
        $this->achatdirect = $achatdirect;
        $this->successMessage = $successMessage;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.negociation');
    }
}
