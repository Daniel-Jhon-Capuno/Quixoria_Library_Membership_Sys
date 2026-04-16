<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_fee', 8, 2);
            $table->integer('borrow_limit_per_week');
            $table->integer('borrow_duration_days');
            $table->boolean('can_reserve')->default(false);
            $table->integer('renewal_limit')->default(0);
            $table->decimal('late_fee_per_day', 5, 2)->default(0);
            $table->integer('priority_level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
