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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('module_type');
            $table->uuid('module_id');
            $table->boolean('status')->default(0);
            $table->string('type');
            $table->string('price');
            $table->string('timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
