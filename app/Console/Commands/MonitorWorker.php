<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

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

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isRunning = false;

        try {
            $process = new Process(['pgrep', '-f', 'php artisan queue:listen']);
            $process->run();

            $isRunning = $process->isSuccessful();
        } catch (\Exception $e) {
            Log::error('Error checking queue worker: ' . $e->getMessage());
        }

        if (!$isRunning) {
            // Démarrer le processus queue:listen
            $startProcess = new Process(['bash', '/home/u474923210/public_html/biicf/monitor_worker.sh']);
            $startProcess->run();

            if ($startProcess->isSuccessful()) {
                Log::info('Queue worker restarted successfully.');
            } else {
                Log::error('Failed to restart queue worker: ' . $startProcess->getErrorOutput());
            }
        } else {
            Log::info('Queue worker is already running.');
        }
    }
}
