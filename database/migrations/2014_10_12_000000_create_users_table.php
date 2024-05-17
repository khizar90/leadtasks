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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('platform')->default('');
            $table->string('platform_id')->default('');
            $table->string('profile_picture')->default('');
            $table->string('country_code')->default('');
            $table->string('phone_number')->default('');
            $table->string('password')->default('');
            $table->string('country')->default('');
            $table->longText('address')->default('');
            $table->longText('about')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
