<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->integer('amount'); // centavos
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, countered, declined, expired
            $table->integer('counter_amount')->nullable();
            $table->text('counter_message')->nullable();
            $table->timestamp('countered_at')->nullable();
            $table->timestamps();

            $table->unique(['listing_id', 'buyer_id', 'status'], 'active_offer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
