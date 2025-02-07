<?php
// Job de prélèvement automatique
namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $tontine;

    public function __construct($user, $tontine)
    {
        $this->user = $user;
        $this->tontine = $tontine;
    }

    public function handle()
    {
        if ($this->user->balance >= $this->tontine->amount) {
            $this->user->balance -= $this->tontine->amount;
            $this->user->save();

            Transaction::create([
                'user_id' => $this->user->id,
                'tontine_id' => $this->tontine->id,
                'amount' => $this->tontine->amount,
                'status' => 'success'
            ]);
        } else {
            Transaction::create([
                'user_id' => $this->user->id,
                'tontine_id' => $this->tontine->id,
                'amount' => $this->tontine->amount,
                'status' => 'failed'
            ]);
        }
    }
}
