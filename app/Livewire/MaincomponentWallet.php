<?php

namespace App\Livewire;

use Livewire\Component;

class MaincomponentWallet extends Component
{
    public $currentPage = 'wallet';

    protected $listeners = ['navigate' => 'setPage'];

    public function setPage($page)
    {
        $this->currentPage = $page;
    }
    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function render()
    {
        return view('livewire.maincomponent-wallet');
    }
}
