<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE borrow_requests MODIFY COLUMN status ENUM('pending', 'confirmed', 'rejected', 'active', 'returned', 'overdue', 'return_requested', 'return_rejected')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE borrow_requests MODIFY COLUMN status ENUM('pending', 'confirmed', 'rejected', 'active', 'returned', 'overdue', 'return_requested')");
    }
};

