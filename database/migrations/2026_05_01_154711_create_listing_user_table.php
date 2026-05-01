<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_user', function (Blueprint $table) {
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['listing_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_user');
    }
};
