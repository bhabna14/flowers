<?php

namespace App\Console\Commands;
use App\Models\Subscription;
use App\Models\SubscriptionPauseResumeLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePausedSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   // Command signature
   protected $signature = 'subscription:update-paused-to-active';

   // Command description
   protected $description = 'Automatically update paused subscriptions to active when the pause period ends';

    /**
     * The console command description.
     *
     * @var string
     */
    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
    
        // Find paused subscriptions where the pause end date has passed
        $subscriptions = Subscription::where('status', 'paused')
            ->where('pause_end_date', '<', $today->addDay()) // Check pause_end_date less than tomorrow
            ->get();
    
        foreach ($subscriptions as $subscription) {
            // Update the status to active
            $subscription->status = 'active';
            $subscription->is_active = true;
            $subscription->pause_start_date = null;
            $subscription->pause_end_date = null;
            $subscription->save();
    
            // Log the status update
            Log::info('Subscription status updated to active', [
                'order_id' => $subscription->order_id,
                'user_id' => $subscription->user_id,
            ]);
        }
    
        $this->info('Subscription statuses updated successfully.');
    
        return Command::SUCCESS;
    }
    
}