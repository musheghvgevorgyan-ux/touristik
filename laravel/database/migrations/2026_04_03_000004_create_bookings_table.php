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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('supplier_ref')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->enum('product_type', ['hotel', 'flight', 'tour', 'transfer', 'package']);
            $table->string('supplier', 50)->nullable();
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 30)->nullable();
            $table->json('product_data')->nullable();
            $table->decimal('net_price', 12, 2);
            $table->decimal('sell_price', 12, 2);
            $table->decimal('commission', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded', 'partial_refund'])->default('unpaid');
            $table->json('supplier_request')->nullable();
            $table->json('supplier_response')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
