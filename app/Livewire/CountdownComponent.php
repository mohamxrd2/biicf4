<?php

namespace App\Livewire;

use App\Models\Countdown;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CountdownComponent extends Component
{
    public $countdownStarted = false;
    public $timeRemaining = null;

    public function startCountdown()
    {
        Countdown::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
        ]);

        $this->countdownStarted = true;
    }

    public function mount()
    {
        $countdown = Countdown::where('user_id', Auth::id())
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if ($countdown) {
            $this->countdownStarted = true;
            $this->updateTimeRemaining();
        }
    }

    public function updateTimeRemaining()
    {
        $countdown = Countdown::where('user_id', Auth::id())
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if ($countdown) {
            $endTime = Carbon::parse($countdown->start_time)->addMinutes(5);
            $now = Carbon::now();

            if ($now->greaterThan($endTime)) {
                $this->timeRemaining = '00:00';
                $this->countdownStarted = false;
            } else {
                $this->timeRemaining = $endTime->diff($now)->format('%I:%S');
            }
        }
    }
    public function render()
    {

        return view('livewire.countdown-component');
    }
}
