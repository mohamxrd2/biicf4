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
        if (function_exists('exec')) {
            shell_exec('bash /home/u474923210/public_html/biicf/monitor_worker.sh');
            Log::info('Exec command ran successfully.');
        } else {
            Log::error('Exec function is disabled.');
        }
    }
}
