<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_view_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamp('viewed_at');

            $table->index(['listing_id', 'ip_address', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_view_logs');
    }
};
