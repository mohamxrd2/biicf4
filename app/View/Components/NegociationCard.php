<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NegociationCard extends Component
{
    public $lastActivity;
    public $nombreParticipants;
    public $isNegociationActive;
    public $commentCount;
    public $name;

    /**
     * Create a new component instance.
     *
     * @param  mixed  $lastActivity
     * @param  int  $nombreParticipants
     * @param  bool  $isNegociationActive
     * @param  int  $commentCount
     */
    public function __construct($name, $lastActivity = null, $nombreParticipants = 0, $isNegociationActive = false, $commentCount = 0)
    {
        $this->lastActivity = $lastActivity;
        $this->nombreParticipants = $nombreParticipants;
        $this->isNegociationActive = $isNegociationActive;
        $this->commentCount = $commentCount;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.negociation-card');
    }
}
