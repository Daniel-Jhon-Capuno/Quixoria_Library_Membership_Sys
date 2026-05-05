<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add new statuses to borrow_requests
        DB::statement("ALTER TABLE borrow_requests MODIFY COLUMN status ENUM(
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
            'appeal_completed'
        )");

        // Add is_restricted to users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_restricted')->default(false)->after('role');
            $table->text('restriction_reason')->nullable()->after('is_restricted');
            $table->timestamp('restricted_at')->nullable()->after('restriction_reason');
        });
    }

    public function down()
    {
        DB::statement("ALTER TABLE borrow_requests MODIFY COLUMN status ENUM(
            'pending',
            'confirmed',
            'rejected',
            'active',
            'returned',
            'overdue',
            'return_requested',
            'return_rejected'
        )");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_restricted', 'restriction_reason', 'restricted_at']);
        });
    }
};

