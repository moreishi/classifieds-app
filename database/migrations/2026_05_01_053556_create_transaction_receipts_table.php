<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users');
            $table->string('buyer_email');
            $table->string('buyer_name')->nullable();
            $table->string('reference_number', 20)->unique();
            $table->integer('amount'); // centavos
            $table->string('status')->default('completed'); // completed, refunded
            $table->timestamp('receipt_sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_receipts');
    }
};
