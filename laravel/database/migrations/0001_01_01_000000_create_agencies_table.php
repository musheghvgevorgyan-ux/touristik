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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->enum('payment_model', ['prepaid', 'credit', 'markup'])->default('prepaid');
            $table->enum('status', ['active', 'suspended', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
