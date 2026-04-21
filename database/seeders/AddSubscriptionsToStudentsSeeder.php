<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MembershipTier;
use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddSubscriptionsToStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Basic membership tier
        $basicTier = MembershipTier::where('name', 'Basic')->first();

        if (!$basicTier) {
            $this->command->error('Basic membership tier not found. Please run MembershipTierSeeder first.');
            return;
        }

        // Find all student users without active subscriptions
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // Check if student already has an active subscription
            $hasSubscription = Subscription::where('user_id', $student->id)
                ->where('status', 'active')
                ->exists();

            if (!$hasSubscription) {
                Subscription::create([
                    'user_id' => $student->id,
                    'membership_tier_id' => $basicTier->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'amount_paid' => 0,
                ]);

                $this->command->info("Basic subscription created for student: {$student->name} ({$student->email})");
            }
        }
    }
}
