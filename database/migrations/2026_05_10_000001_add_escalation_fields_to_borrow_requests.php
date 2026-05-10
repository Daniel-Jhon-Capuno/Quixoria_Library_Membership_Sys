<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->string('escalation_level')->nullable()->after('status');
            $table->unsignedBigInteger('replacement_fee_cents')->nullable()->after('escalation_level');
            $table->boolean('replacement_fee_paid')->default(false)->after('replacement_fee_cents');
            $table->timestamp('resolved_at')->nullable()->after('replacement_fee_paid');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('resolved_at');
            $table->timestamp('temporary_unblock_until')->nullable()->after('resolved_by');
        });
    }

    public function down()
    {
        Schema::table('borrow_requests', function (Blueprint $table) {
            $table->dropColumn([
                'escalation_level',
                'replacement_fee_cents',
                'replacement_fee_paid',
                'resolved_at',
                'resolved_by',
                'temporary_unblock_until',
            ]);
        });
    }
};
