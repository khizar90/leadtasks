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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('category_name');
            $table->string('title');
            $table->longText('requirement')->default('');
            $table->longText('description')->default('');
            $table->string('date')->default('');
            $table->boolean('is_flexible')->default(false);
            $table->string('budget_type');
            $table->string('budget');
            $table->string('location');
            $table->string('lat');
            $table->string('lng');
            $table->boolean('is_remote')->default(false);
            $table->string('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
