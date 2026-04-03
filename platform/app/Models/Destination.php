<?php

namespace App\Models;

use Core\Model;

class Destination extends Model
{
    protected static string $table = 'destinations';

    public static function featured(int $limit = 6): array
    {
        return self::where(['featured' => 1, 'status' => 'active'], 'name ASC', $limit);
    }

    public static function active(): array
    {
        return self::where(['status' => 'active'], 'name ASC');
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }
}
