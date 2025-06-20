<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('stripe_id')->nullable();
            $table->decimal('amount_off', 8, 2)->nullable();
            $table->decimal('percent_off', 5, 2)->nullable();
            $table->string('duration');
            $table->string('applies_to')->nullable();
            $table->boolean('synced')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
