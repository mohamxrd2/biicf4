<?php

namespace App\Console\Commands;

use App\Jobs\Provision as JobsProvision;
use App\Models\AjoutMontant;
use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\Projet;
use App\Models\Promir;
use App\Models\User;
use App\Notifications\GagnantProjetNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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
