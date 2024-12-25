<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\CountdownTick;

class RunCountdowns extends Command
{
    protected $signature = 'countdown:run';
    protected $description = 'Run the countdown timer';
    public function handle()
    {
        $timeRemaining = 300; // 5 minutes
        while ($timeRemaining > 0) {
            broadcast(new CountdownTick($timeRemaining));
            sleep(1);
            $timeRemaining--;
        }
    }
}
