<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BottomNavigation extends Component
{
    public $unreadCount;

    public function __construct($unreadCount = 0)
    {
        $this->unreadCount = $unreadCount;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.bottom-navigation');
    }
}
