<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_unpaid_fees')->default(false)->after('remember_token');
            $table->boolean('is_permanently_banned')->default(false)->after('has_unpaid_fees');
            $table->text('ban_reason')->nullable()->after('is_permanently_banned');
            $table->timestamp('banned_at')->nullable()->after('ban_reason');
            $table->unsignedBigInteger('banned_by')->nullable()->after('banned_at');
            $table->timestamp('temporary_unblock_until')->nullable()->after('banned_by');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'has_unpaid_fees',
                'is_permanently_banned',
                'ban_reason',
                'banned_at',
                'banned_by',
                'temporary_unblock_until',
            ]);
        });
    }
};
