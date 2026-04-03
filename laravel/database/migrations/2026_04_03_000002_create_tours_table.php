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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['ingoing', 'outgoing', 'transfer']);
            $table->text('description')->nullable();
            $table->json('itinerary')->nullable();
            $table->string('duration', 50)->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
