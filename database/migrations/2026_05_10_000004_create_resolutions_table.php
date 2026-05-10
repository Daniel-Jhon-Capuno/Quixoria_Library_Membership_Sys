<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_request_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('amount_cents')->default(0);
            $table->string('method')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resolutions');
    }
};
