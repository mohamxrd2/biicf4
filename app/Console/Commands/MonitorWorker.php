<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor the queue worker and restart if needed';
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Commande shell pour vérifier et relancer le worker si nécessaire
        $scriptPath = '/home/u474923210/public_html/biicf/monitor_worker.sh';

        // Exécuter le script shell
        $output = null;
        $resultCode = null;
        exec("bash $scriptPath", $output, $resultCode);

        // Vérifiez si l'exécution a réussi et consignez la sortie
        if ($resultCode === 0) {
            Log::info('MonitorWorker command executed successfully.');
        } else {
            Log::error('MonitorWorker command failed. Result code: ' . $resultCode);
        }
    }
}
