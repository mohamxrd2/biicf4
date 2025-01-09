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
            $isRunning = shell_exec('pgrep -f "php artisan queue:listen"');

            if (!$isRunning) {
                // Si le processus n'est pas actif, le démarrer et le détacher de la session
                shell_exec('bash /home/u474923210/public_html/biicf/monitor_worker.sh');
                Log::info('Queue worker restarted and detached successfully.');
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
