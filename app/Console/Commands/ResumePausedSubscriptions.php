<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class ResumePausedSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:resume-paused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resume paused subscriptions if the pause_end_date is reached or passed';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('ResumePausedSubscriptions command started.');

        // Get today's date
        $today = Carbon::today();

        // Find paused subscriptions where pause_end_date is today or earlier
        $pausedSubscriptions = Subscription::where('status', 'paused')
            ->where('pause_end_date', '<=', $today)
            ->get();

        // Log the count of subscriptions being updated
        \Log::info('Paused subscriptions to resume:', ['count' => $pausedSubscriptions->count()]);

        foreach ($pausedSubscriptions as $subscription) {
            $subscription->status = 'active';
            $subscription->save();

            // Log each subscription updated
            \Log::info('Subscription resumed:', ['subscription_id' => $subscription->id]);
        }

        \Log::info('ResumePausedSubscriptions command completed.');
        
        return 0;
    }
}
