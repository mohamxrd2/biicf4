<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorWorker extends Command
{
    protected $signature = 'worker:monitor';
    protected $description = 'Monitor the queue worker and restart if needed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get base path and determine OS
        $basePath = base_path();
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        // Set script path based on OS
        $scriptPath = $isWindows
            ? $basePath . '\monitor_worker.bat'
            : $basePath . '/monitor_worker.sh';

        try {
            // Execute appropriate command based on OS
            $command = $isWindows ? $scriptPath : "bash {$scriptPath}";
            $output = [];
            $resultCode = null;

            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                Log::info('MonitorWorker command executed successfully.');
                $this->info('Worker monitored successfully.');
            } else {
                Log::error('MonitorWorker failed. Code: ' . $resultCode);
                $this->error('Worker monitoring failed.');
            }
        } catch (\Exception $e) {
            Log::error('MonitorWorker exception: ' . $e->getMessage());
            $this->error('Error executing worker monitor: ' . $e->getMessage());
        }
    }
}
