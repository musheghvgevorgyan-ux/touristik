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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('transaction_id')->nullable();
            $table->string('gateway', 50);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('method', ['card', 'bank_transfer', 'cash', 'balance']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partial_refund'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->text('refund_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
