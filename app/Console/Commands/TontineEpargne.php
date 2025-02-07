<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tontine;
use App\Jobs\ProcessPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TontineEpargne extends Command
{
    protected $signature = 'app:epargne';
    protected $description = 'Check if the time is finished to submit a notification to consumption user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $tontines = Tontine::with('users')->get();
            foreach ($tontines as $tontine) {
                foreach ($tontine->users as $user) {
                    dispatch(new ProcessPayment($user, $tontine));
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur dans AjoutQoffre', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Une erreur est survenue. Vérifie les logs.');
        }
    }
}
