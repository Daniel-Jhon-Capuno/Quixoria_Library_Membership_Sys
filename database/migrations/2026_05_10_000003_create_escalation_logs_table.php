<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_request_id')->constrained()->onDelete('cascade');
            $table->string('level');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('escalation_logs');
    }
};
