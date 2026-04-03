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
        Schema::create('flight_prices', function (Blueprint $table) {
            $table->id();
            $table->string('from_city');
            $table->string('to_city');
            $table->decimal('price', 10, 2);
            $table->enum('trip_type', ['oneway', 'roundtrip']);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['from_city', 'to_city', 'trip_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_prices');
    }
};
