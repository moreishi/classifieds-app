<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->integer('price'); // centavos
            $table->string('condition')->nullable(); // brand_new, like_new, used, for_parts
            $table->string('status')->default('active'); // active, sold, expired, flagged
            $table->boolean('is_featured')->default(false);
            $table->integer('total_views')->default(0);
            $table->integer('unique_views')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('sold_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category_id', 'status', 'created_at']);

            if (DB::connection()->getDriverName() === 'mysql') {
                $table->fullText(['title', 'description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
