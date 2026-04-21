<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MembershipTier;
use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Basic membership tier
        $basicTier = MembershipTier::where('name', 'Basic')->first();

        // Create student 1
        $student1 = User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => 'password',
            'role' => 'student',
        ]);
        $this->createSubscription($student1, $basicTier);

        // Create student 2
        $student2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'role' => 'student',
        ]);
        $this->createSubscription($student2, $basicTier);

        // Create student 3
        $student3 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password',
            'role' => 'student',
        ]);
        $this->createSubscription($student3, $basicTier);
    }

    private function createSubscription(User $user, MembershipTier $tier)
    {
        Subscription::create([
            'user_id' => $user->id,
            'membership_tier_id' => $tier->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'amount_paid' => 0,
        ]);
    }
}
