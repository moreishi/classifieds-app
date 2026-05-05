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
            if (! Schema::hasColumn('users', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('oauth_id');
            }
        });

        // Make password nullable separately to avoid column-exists issues
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->nullable()->change();
            });
        } catch (\Exception $e) {
            // Column may already be nullable — safe to ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'oauth_id')) {
                $table->dropColumn('oauth_id');
            }

            if (Schema::hasColumn('users', 'oauth_provider')) {
                $table->dropColumn('oauth_provider');
            }
        });
    }
};
