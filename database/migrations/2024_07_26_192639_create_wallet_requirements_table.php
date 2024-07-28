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
        Schema::create('wallet_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->boolean('status')->default(0);
            $table->string('text');
            $table->string('type');
            $table->string('subtype');
            $table->string('key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_requirements');
    }
};
