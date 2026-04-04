<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'country',
        'price_from',
        'image_url',
        'color',
        'emoji',
        'latitude',
        'longitude',
        'featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_from' => 'decimal:2',
            'featured' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    // ─── Helper Methods ──────────────────────────────────────────

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
