<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('emoji');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        // Update existing destinations with coordinates
        $coords = [
            'paris' => [48.8566, 2.3522],
            'tokyo' => [35.6762, 139.6503],
            'bali' => [-8.3405, 115.0920],
            'rome' => [41.9028, 12.4964],
            'new-york' => [40.7128, -74.0060],
            'maldives' => [3.2028, 73.2207],
        ];

        foreach ($coords as $slug => $latlng) {
            DB::table('destinations')
                ->where('slug', $slug)
                ->update(['latitude' => $latlng[0], 'longitude' => $latlng[1]]);
        }
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
