<?php

namespace App\Suppliers;

use App\Suppliers\Hotelbeds\HotelbedsAdapter;

class SupplierFactory
{
    private static array $adapters = [];

    /**
     * Get a supplier adapter by name
     */
    public static function make(string $supplier): SupplierInterface
    {
        if (!isset(self::$adapters[$supplier])) {
            self::$adapters[$supplier] = match ($supplier) {
                'hotelbeds' => new HotelbedsAdapter(),
                // Future suppliers:
                // 'amadeus'  => new Amadeus\AmadeusAdapter(),
                // 'local'    => new Local\LocalToursAdapter(),
                default     => throw new \RuntimeException("Unknown supplier: {$supplier}"),
            };
        }

        return self::$adapters[$supplier];
    }

    /**
     * Get all available supplier names
     */
    public static function available(): array
    {
        return ['hotelbeds'];
    }
}
