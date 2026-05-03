<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan'); // 'bump_7d', 'bump_14d', 'bump_30d'
            $table->integer('amount_paid'); // centavos: 5000 (₱50) for 7d
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['listing_id', 'is_active']);
            $table->index(['expires_at', 'is_active']);
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->timestamp('featured_until')->nullable()->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_promotions');
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('featured_until');
        });
    }
};
