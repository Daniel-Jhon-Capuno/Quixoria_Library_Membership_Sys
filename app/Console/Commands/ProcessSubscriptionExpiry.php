<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiryReminderNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Carbon\Carbon;

#[Signature('subscriptions:check-expiry')]
#[Description('Process subscription expiry and send reminders')]
class ProcessSubscriptionExpiry extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing subscription expiry...');

        // Process expired subscriptions
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->with('user')
            ->get();

        $this->info("Found {$expiredSubscriptions->count()} expired subscriptions");

        foreach ($expiredSubscriptions as $subscription) {
            /** @var \App\Models\Subscription $subscription */
            // Update status to expired
            $subscription->update(['status' => 'expired']);

            // Send expired notification
            $subscription->user->notify(new SubscriptionExpiredNotification());

            $this->line("Marked subscription for {$subscription->user->email} as expired");
        }

        // Send reminders for subscriptions expiring in 7 days
        $sevenDaysFromNow = Carbon::now()->addDays(7)->toDateString();
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereDate('ends_at', $sevenDaysFromNow)
            ->with('user')
            ->get();

        $this->info("Found {$expiringSubscriptions->count()} subscriptions expiring in 7 days");

        foreach ($expiringSubscriptions as $subscription) {
            // Send expiry reminder notification
            $subscription->user->notify(new SubscriptionExpiryReminderNotification());

            $this->line("Sent expiry reminder to {$subscription->user->email}");
        }

        $totalProcessed = $expiredSubscriptions->count() + $expiringSubscriptions->count();
        $this->info("Successfully processed {$totalProcessed} subscriptions");
    }
}
