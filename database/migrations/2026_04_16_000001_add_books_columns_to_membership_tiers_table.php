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
        Schema::table('membership_tiers', function (Blueprint $table) {
            if (!Schema::hasColumn('membership_tiers', 'books_per_month')) {
                $table->integer('books_per_month')->default(4)->after('borrow_limit_per_week');
            }
            if (!Schema::hasColumn('membership_tiers', 'borrow_duration')) {
                $table->integer('borrow_duration')->default(14)->after('borrow_duration_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn('books_per_month');
            $table->dropColumn('borrow_duration');
        });
    }
};
