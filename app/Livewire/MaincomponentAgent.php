<?php

namespace App\Livewire;

use Livewire\Component;

class MaincomponentAgent extends Component
{
    public $currentPage = 'agent';

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
        return view('livewire.maincomponent-agent');
    }
}
