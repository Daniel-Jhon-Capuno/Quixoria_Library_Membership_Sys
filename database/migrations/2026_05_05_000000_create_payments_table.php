<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_tier_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['purchase', 'upgrade', 'downgrade']);
            $table->enum('status', ['pending', 'confirmed', 'rejected']);
            $table->decimal('amount', 10, 2);
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->enum('payment_method', ['gcash', 'maya', 'bank_transfer']);
            $table->string('reference_number');
            $table->string('proof_of_payment')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
