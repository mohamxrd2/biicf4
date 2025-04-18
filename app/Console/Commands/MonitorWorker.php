<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

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
        $this->info('🔍 Vérification du statut du worker...');
        Log::info('🔍 Vérification du statut du worker...');

        // Vérification des jobs échoués
        $this->handleFailedJobs();

        // Vérifier si le worker tourne déjà
        if (!$this->isWorkerRunning()) {
            $this->restartQueueWorker();
        } else {
            $this->info('✅ Worker déjà en cours d\'exécution.');
            Log::info('✅ Worker déjà en cours d\'exécution.');
        }

        return Command::SUCCESS;
    }

    /**
     * Vérifie si un worker est actif
     */
    private function isWorkerRunning(): bool
    {
        $process = new Process(['pgrep', '-f', 'queue:work']);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Gère les jobs échoués
     */
    private function handleFailedJobs(): void
    {
        $failedJobs = DB::table('failed_jobs')->count();

        if ($failedJobs > 0) {
            $this->warn("⚠️ {$failedJobs} jobs échoués détectés. Tentative de relance...");
            Log::warning("⚠️ {$failedJobs} jobs échoués détectés. Tentative de relance...");

            $retryProcess = new Process(['php', base_path('artisan'), 'queue:retry', 'all']);
            $retryProcess->run();

            if ($retryProcess->isSuccessful()) {
                $this->info('✅ Jobs relancés avec succès !');
                Log::info('✅ Jobs relancés avec succès !');
            } else {
                $this->error('❌ Échec de la relance des jobs.');
                Log::error('❌ Échec de la relance des jobs.');
            }
        } else {
            $this->info('✅ Aucun job échoué.');
            Log::info('✅ Aucun job échoué.');
        }
    }

    /**
     * Redémarre le worker
     */
    private function restartQueueWorker(): void
    {
        $this->warn('🚀 Redémarrage du worker...');
        Log::warning('🚀 Redémarrage du worker...');

        // Stopper proprement l'ancien worker
        $stopProcess = new Process(['php', base_path('artisan'), 'queue:restart']);
        $stopProcess->run();

        // Lancer un nouveau worker (correction de la commande)
        $startProcess = new Process([
            'php',
            base_path('artisan'),
            'queue:work',
            '--tries=3',
            '--timeout=90'
        ]);

        // Exécuter en arrière-plan correctement
        $startProcess->setOptions(['create_new_console' => true]);
        $startProcess->disableOutput();
        $startProcess->start();

        // Attendre un court moment pour vérifier si le processus a bien démarré
        sleep(2);

        if ($this->isWorkerRunning()) {
            $this->info('✅ Worker redémarré avec succès.');
            Log::info('✅ Worker redémarré avec succès.');
        } else {
            $this->error('❌ Échec du redémarrage du worker.');
            Log::error('❌ Échec du redémarrage du worker.');
        }
    }
}
