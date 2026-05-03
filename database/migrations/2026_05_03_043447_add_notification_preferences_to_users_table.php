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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_new_inquiry')->default(true)->after('last_active_at');
            $table->boolean('notify_seller_reply')->default(true)->after('notify_new_inquiry');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notify_new_inquiry');
            $table->dropColumn('notify_seller_reply');
        });
    }
};
