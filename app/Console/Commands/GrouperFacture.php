<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Countdown;
use App\Models\User;
use App\Notifications\GrouperFactureNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class GrouperFacture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:grouper-facture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve unnotified countdowns where the start_time is at least one minute past
        $countdowns = Countdown::where('notified', false)
            ->where('start_time', '<=', now()->subMinutes(1))
            ->get();

        foreach ($countdowns as $countdown) {
            // Retrieve the unique code
            $code_unique = $countdown->code_unique;

            // Get the comment with the lowest price for the given unique code
            $lowestPriceComment = Comment::with('user')
                ->where('code_unique', $code_unique)
                ->orderBy('prixTrade', 'asc')
                ->first();

            if ($lowestPriceComment && $countdown->difference === 'facturegrouper') {
                $details = [
                    'code_unique' => $code_unique,
                ];

                // Send notification to the user
                Notification::send($lowestPriceComment->user, new GrouperFactureNotifications($details));
            }

            // Update the notified status to true
            $countdown->update(['notified' => true]);
        }
    }
}
