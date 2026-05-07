<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Populate slugs for existing regions
        $regionSlugs = [
            'Central Visayas' => 'central-visayas',
            'Eastern Visayas' => 'eastern-visayas',
            'Western Visayas' => 'western-visayas',
            'Zamboanga Peninsula' => 'zamboanga-peninsula',
            'Northern Mindanao' => 'northern-mindanao',
            'Davao Region' => 'davao',
            'SOCCSKSARGEN' => 'soccsksargen',
            'Caraga' => 'caraga',
            'BARMM' => 'barmm',
        ];

        foreach ($regionSlugs as $name => $slug) {
            DB::table('regions')->where('name', $name)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
