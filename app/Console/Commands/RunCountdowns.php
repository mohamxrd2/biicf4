<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunCountdowns extends Command
{
    protected $signature = 'countdowns:work';
    protected $description = 'Démarre le worker pour traiter les files d\'attente des countdowns';

    public function handle()
    {
        $this->info('Démarrage du worker pour les countdowns...');

        // Exécuter une seule fois au lieu d'une boucle infinie
        Artisan::call('queue:work', [
            '--queue' => 'default',
            '--tries' => 1,
            '--timeout' => 30,
            '--sleep' => 3,
            '--max-jobs' => 100,
            '--stop-when-empty' => true
        ]);
    }
}
