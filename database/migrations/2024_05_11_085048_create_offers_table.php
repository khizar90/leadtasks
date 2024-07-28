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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignUuid('to_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('job_id');
            $table->string('budget');
            $table->string('time');
            $table->longText('description');
            $table->string('status')->default(0);
            $table->string('accept_time')->default('');
            $table->string('start_time')->default('');
            $table->string('complete_time')->default('');
            $table->string('payment_id')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
