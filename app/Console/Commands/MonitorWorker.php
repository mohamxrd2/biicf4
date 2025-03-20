<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MonitorWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor the queue worker and restart if needed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting queue worker monitoring...');
        $isRunning = $this->checkQueueWorkerStatus();

        // Check for failed jobs
        $this->handleFailedJobs();

        // Restart worker if needed
        if (!$isRunning) {
            $this->restartQueueWorker();
        } else {
            $this->info('Queue worker is already running.');
            Log::info('Queue worker is already running.');
        }

        return Command::SUCCESS;
    }

    /**
     * Check if the queue worker is running
     *
     * @return bool
     */
    private function checkQueueWorkerStatus(): bool
    {
        try {
            $process = new Process(['pgrep', '-f', 'queue:listen']);
            $process->run();

            $isRunning = $process->isSuccessful();

            if ($isRunning) {
                $this->info('Queue worker status: Running');
            } else {
                $this->warn('Queue worker status: Not running');
            }

            return $isRunning;
        } catch (\Exception $e) {
            $this->error('Error checking queue worker: ' . $e->getMessage());
            Log::error('Error checking queue worker: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle failed jobs
     *
     * @return void
     */
    private function handleFailedJobs(): void
    {
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();

            if ($failedJobsCount > 0) {
                $this->warn("⚠️ {$failedJobsCount} failed jobs detected. Attempting to retry...");
                Log::warning("⚠️ {$failedJobsCount} jobs ont échoué. Tentative de relance...");

                $retryProcess = new Process(['php', 'artisan', 'queue:retry', 'all']);
                $retryProcess->run();

                if ($retryProcess->isSuccessful()) {
                    $this->info("✅ All failed jobs have been successfully retried!");
                    Log::info("✅ Tous les jobs échoués ont été relancés avec succès !");
                } else {
                    $this->error("❌ Failed to retry jobs: " . $retryProcess->getErrorOutput());
                    Log::error("❌ Échec de la relance des jobs : " . $retryProcess->getErrorOutput());
                }
            } else {
                $this->info("✅ No failed jobs detected.");
                Log::info("✅ Aucun job échoué détecté.");
            }
        } catch (\Exception $e) {
            $this->error('Error handling failed jobs: ' . $e->getMessage());
            Log::error('Error handling failed jobs: ' . $e->getMessage());
        }
    }

    /**
     * Restart the queue worker
     *
     * @return void
     */
    private function restartQueueWorker(): void
    {
        $this->info('Attempting to restart queue worker...');

        try {
            $startProcess = new Process(['php', 'artisan', 'queue:listen', '--daemon', '--tries=3', '--timeout=90']);
            $startProcess->setTimeout(60);
            $startProcess->run();

            if ($startProcess->isSuccessful()) {
                $this->info('Queue worker restarted successfully.');
                Log::info('Queue worker restarted successfully.');
            } else {
                $error = $startProcess->getErrorOutput();
                $this->error('Failed to restart queue worker: ' . $error);
                Log::error('Failed to restart queue worker: ' . $error);
            }
        } catch (\Exception $e) {
            $this->error('Exception while restarting queue worker: ' . $e->getMessage());
            Log::error('Exception while restarting queue worker: ' . $e->getMessage());
        }
    }
}
