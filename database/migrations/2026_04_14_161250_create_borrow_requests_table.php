<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrow_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'confirmed',
                'rejected',
                'active',
                'returned',
                'overdue',
                'return_requested',
                'return_rejected',
                'appeal_scheduled',
                'appeal_no_show',
                'appeal_rescheduled',
                'appeal_failed',
                'appeal_completed',
            ])->default('pending');
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->text('appeal_reason')->nullable();
            $table->datetime('appeal_scheduled_at')->nullable();
            $table->enum('appeal_status', ['none', 'pending', 'scheduled', 'rescheduled', 'completed', 'failed'])->default('none');
            $table->integer('renewals_used')->default(0);
            $table->timestamp('borrowed_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->decimal('late_fee_charged', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_requests');
    }
};