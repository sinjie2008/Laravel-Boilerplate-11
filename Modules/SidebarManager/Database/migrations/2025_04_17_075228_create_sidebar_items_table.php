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
        Schema::create('sidebar_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('route')->nullable(); // Can be a route name or a URL
            $table->unsignedBigInteger('parent_id')->nullable()->index(); // For nested menus
            $table->integer('order')->default(0);
            $table->string('permission_required')->nullable(); // Optional permission check
            $table->string('module')->nullable(); // Optional: Associate with a module
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            // Optional: Add foreign key constraint if you want strict parent relations
            // $table->foreign('parent_id')->references('id')->on('sidebar_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_items');
    }
};
