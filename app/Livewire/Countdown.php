<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use Livewire\Component;

class Countdown extends Component
{

    public $timeRemaining;
    public $isRunning = false;
    public function mount()
    {
        $this->timeRemaining = 300; // 5 minutes en secondes
    }
    public function startCountdown()
    {
        $this->isRunning = true;
        event(new CountdownStarted($this->timeRemaining));
    }
    public function getListeners()
    {
        return [
            "echo:countdown,CountdownStarted" => 'handleCountdownStart',
            "echo:countdown,CountdownTick" => 'handleCountdownTick',
        ];
    }
    public function handleCountdownStart($event)
    {
        $this->timeRemaining = $event['time'];
        $this->isRunning = true;
    }
    public function handleCountdownTick($event)
    {
        $this->timeRemaining = $event['time'];
    }
    public function render()
    {
        return view('livewire.countdown');
    }
}
