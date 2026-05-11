<?php

namespace App\Console\Commands;

use App\Models\MembershipTier;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class AssignBasicSubscriptions extends Command
{
    protected $signature = 'subscriptions:assign-basic';
    protected $description = 'Assign Basic tier subscriptions to students who don\'t have any';

    public function handle()
    {
        // Get or create Basic tier
        $basicTier = MembershipTier::where('name', 'Basic')->first();
        
        if (!$basicTier) {
            $this->error('❌ Basic tier not found in database!');
            return 1;
        }

        $this->info("✅ Found Basic tier: {$basicTier->name} (ID: {$basicTier->id})");

        // Find all students without an active subscription
        $studentsWithoutSubscription = User::where('role', 'student')
            ->whereDoesntHave('subscription')
            ->get();

        $count = $studentsWithoutSubscription->count();
        
        if ($count === 0) {
            $this->info('✅ All students already have subscriptions!');
            return 0;
        }

        $this->info("📝 Found {$count} students without subscriptions. Assigning Basic tier...");

        foreach ($studentsWithoutSubscription as $user) {
            try {
                Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $basicTier->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => null,
                    'amount_paid' => 0,
                ]);
                $this->line("  ✓ {$user->name} ({$user->email}) - assigned Basic tier");
            } catch (\Exception $e) {
                $this->warn("  ✗ {$user->name} ({$user->email}) - failed: {$e->getMessage()}");
            }
        }

        $this->info("✅ Done! All students now have Basic tier subscriptions.");
        return 0;
    }
}
