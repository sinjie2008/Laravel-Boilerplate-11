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
        Schema::create('sql_generator_settings', function (Blueprint $table) {
            $table->id();
            $table->text('api_url')->nullable(); // Store the API URL
            $table->text('api_key')->nullable(); // Store the API Key (consider encryption later)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sql_generator_settings');
    }
};
