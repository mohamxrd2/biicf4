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
            // Vérifiez si la fonction `shell_exec` est disponible
            if (function_exists('shell_exec')) {
                $output = shell_exec('bash /home/u474923210/public_html/biicf/monitor_worker.sh');

                if ($output === null) {
                    Log::warning('The shell script did not return any output.');
                } else {
                    Log::info('Shell script executed successfully:', ['output' => $output]);
                }
            } else {
                Log::error('The shell_exec function is disabled on this server.');
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while running the MonitorWorker command.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
