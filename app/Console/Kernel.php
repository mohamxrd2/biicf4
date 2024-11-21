<?php

namespace App\Console;

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
        if (App::environment('local')) {
            //En local, exécuter la commande toutes les minutes
            // $schedule->command('check:countdowns')->everyMinute();
            // $schedule->command('app:appeloffre')->everyMinute();
            // $schedule->command('app:ajout-qoffre')->everyMinute();
            // $schedule->command('app:credit-countdown')->everyMinute();
            // $schedule->command('app:projet-countdown')->everyMinute();
            $schedule->command('app:rappel-journalieres-credits')->everyMinute();
            // $schedule->command('app:rappel-journalieres-projets')->everyMinute();
            // $schedule->command('app:finacementProjetAccorde')->everyMinute();
            // $schedule->command('app:finacementCredits')->everyMinute();

        } else {
            // Sur le serveur en ligne, exécuter la commande avec une expression cron spécifique
            $schedule->command('check:countdowns')->cron('* * * * *');
            $schedule->command('app:appeloffre')->cron('* * * * *');
            $schedule->command('app:ajout-qoffre')->cron('* * * * *');
            $schedule->command('app:credit-countdown')->cron('* * * * *');
            $schedule->command('app:projet-countdown')->cron('* * * * *');
            $schedule->command('app:rappel-journalieres-credits')->cron('* * * * *');
            $schedule->command('app:rappel-journalieres-projets')->cron('* * * * *');
            $schedule->command('app:finacementProjetAccorde')->cron('* * * * *');
            $schedule->command('app:finacementCredits')->cron('* * * * *');
        }
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
