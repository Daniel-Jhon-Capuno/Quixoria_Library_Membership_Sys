<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Columns already added in create_borrow_requests_table migration
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};