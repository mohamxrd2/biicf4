<?php

namespace App\Console;

use App\Jobs\Provision;
use Illuminate\Support\Facades\App;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //En local, exécuter la commande toutes les minutes
        $schedule->command('check:countdowns')->everyMinute();
        $schedule->command('app:appeloffreGrouper')->everyMinute();
        $schedule->command('app:ajout-qoffre')->everyMinute();
        $schedule->command('app:process-payments')->everyMinute();
        $schedule->command('app:credit-countdown')->everyMinute();
        $schedule->command('app:projet-countdown')->everyMinute();
        $schedule->command('app:rappel-journalieres-credits')->everyMinute();
        $schedule->command('app:rappel-journalieres-projets')->everyMinute();
        $schedule->command('app:finacementProjetAccorde')->everyMinute();
        $schedule->command('app:finacementCredits')->everyMinute();
        // Redémarrer les workers chaque minute pour garantir leur bon fonctionnement
        $schedule->command('app:monitor')->everyMinute();
        $schedule->command('app:provisison')->dailyAt('18:00');
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
