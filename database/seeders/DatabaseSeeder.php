<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\MembershipTierSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\StaffUserSeeder;
use Database\Seeders\StudentUserSeeder;
use Database\Seeders\BookSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MembershipTierSeeder::class,
            AdminUserSeeder::class,
            StaffUserSeeder::class,
            StudentUserSeeder::class,
            BookSeeder::class,
        ]);
    }
}
