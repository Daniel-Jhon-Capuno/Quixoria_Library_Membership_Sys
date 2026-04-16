<?php

namespace Database\Seeders;

use App\Models\MembershipTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MembershipTier::create([
            'name' => 'Basic',
            'monthly_fee' => 99,
            'borrow_limit_per_week' => 2,
            'borrow_duration_days' => 7,
            'can_reserve' => false,
            'renewal_limit' => 0,
            'late_fee_per_day' => 5,
            'priority_level' => 1,
        ]);

        MembershipTier::create([
            'name' => 'Standard',
            'monthly_fee' => 199,
            'borrow_limit_per_week' => 5,
            'borrow_duration_days' => 14,
            'can_reserve' => false,
            'renewal_limit' => 1,
            'late_fee_per_day' => 3,
            'priority_level' => 2,
        ]);

        MembershipTier::create([
            'name' => 'Premium',
            'monthly_fee' => 349,
            'borrow_limit_per_week' => 10,
            'borrow_duration_days' => 21,
            'can_reserve' => true,
            'renewal_limit' => 2,
            'late_fee_per_day' => 1,
            'priority_level' => 3,
        ]);
    }
}
