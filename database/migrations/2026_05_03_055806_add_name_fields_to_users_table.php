
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('username');
            $table->string('middle_name', 100)->nullable()->after('first_name');
            $table->string('last_name', 100)->nullable()->after('middle_name');
        });

        // Backfill: split existing `name` into first/middle/last
        DB::table('users')->orderBy('id')->each(function ($user) {
            if ($user->first_name || ! $user->name) {
                return;
            }

            $parts = preg_split('/\s+/', trim($user->name));
            $firstName = $parts[0] ?? null;
            $lastName = count($parts) > 1 ? array_pop($parts) : null;
            $middleName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
    }
};
