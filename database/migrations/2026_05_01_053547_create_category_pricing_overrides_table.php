<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_pricing_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->integer('post_price');
            $table->integer('free_listings_unverified')->nullable();
            $table->integer('free_listings_verified')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_pricing_overrides');
    }
};
