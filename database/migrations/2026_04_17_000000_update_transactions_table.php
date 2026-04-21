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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('borrow_request_id')->nullable()->after('subscription_id')->constrained('borrow_requests')->onDelete('cascade');
            $table->string('description')->nullable()->after('reference_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['borrow_request_id']);
            $table->dropColumn(['borrow_request_id', 'description']);
        });
    }
};
