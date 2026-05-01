<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gcash_number', 11)->nullable()->unique()->after('password');
            $table->timestamp('gcash_verified_at')->nullable()->after('gcash_number');
            $table->foreignId('city_id')->nullable()->constrained()->after('gcash_verified_at');
            $table->integer('reputation_points')->default(0)->after('city_id');
            $table->string('reputation_tier')->default('newbie')->after('reputation_points');
            $table->integer('free_listings_used')->default(0)->after('reputation_tier');
            $table->timestamp('free_listings_reset_at')->nullable()->after('free_listings_used');
            $table->integer('credit_balance')->default(0)->after('free_listings_reset_at');
            $table->string('referral_code', 8)->unique()->nullable()->after('credit_balance');
            $table->foreignId('referred_by')->nullable()->constrained('users')->after('referral_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropColumn([
                'gcash_number',
                'gcash_verified_at',
                'city_id',
                'reputation_points',
                'reputation_tier',
                'free_listings_used',
                'free_listings_reset_at',
                'credit_balance',
                'referral_code',
            ]);
        });
    }
};
