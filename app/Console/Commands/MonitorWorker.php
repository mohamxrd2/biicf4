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
        try {
            // Vérifier si le processus "php artisan queue:listen" est actif
            $processCheck = new Process(['pgrep', '-f', 'php artisan queue:listen']);
            $processCheck->run();

            if (!$processCheck->isSuccessful()) {
                // Si aucun processus actif, démarrer le worker en arrière-plan
                $processStart = new Process([
                    'nohup',
                    'php',
                    '/home/u474923210/public_html/biicf/artisan',
                    'queue:listen',
                    '--tries=3',
                    '--sleep=3',
                    '>',
                    '/home/u474923210/public_html/biicf/storage/logs/queue.log',
                    '2>&1',
                    '&',
                    'disown'
                ]);
                $processStart->start();

                Log::info('Queue worker restarted successfully.');
            } else {
                Log::info('Queue worker is already running.');
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while monitoring the queue worker.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
