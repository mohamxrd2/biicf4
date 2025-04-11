<?php

namespace App\Console\Commands;

use App\Jobs\Provision as JobsProvision;

use App\Models\Promir;
use Illuminate\Console\Command;

class Provision extends Command
{
    protected $signature = 'app:provisison';
    protected $description = 'Commande pour retirer les revenu alloué par jour';


    public function handle()
    {

       $userPromir = Promir::all();

       foreach ($userPromir as $user) {
            dispatch(new JobsProvision($user->user_id));
       }
    }
}
