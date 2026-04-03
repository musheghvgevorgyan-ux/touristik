<?php

namespace App\Models;

use Core\Model;

class Tour extends Model
{
    protected static string $table = 'tours';

    /**
     * Find a tour by its URL slug
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    /**
     * Get tours by type (ingoing, outgoing, transfer)
     */
    public static function byType(string $type, int $limit = 50): array
    {
        return self::where(
            ['type' => $type, 'status' => 'active'],
            'featured DESC, title ASC',
            $limit
        );
    }

    /**
     * Get featured tours (across all types)
     */
    public static function featured(int $limit = 6): array
    {
        return self::where(
            ['featured' => 1, 'status' => 'active'],
            'type ASC, title ASC',
            $limit
        );
    }

    /**
     * Get all active tours
     */
    public static function active(): array
    {
        return self::where(['status' => 'active'], 'type ASC, title ASC');
    }
}
