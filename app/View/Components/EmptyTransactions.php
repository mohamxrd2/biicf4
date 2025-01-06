<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyTransactions extends Component
{
    public function render()
    {
        return view('components.empty-transactions');
    }
}
