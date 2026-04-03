<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'description',
        'itinerary',
        'duration',
        'price_from',
        'image_url',
        'destination_id',
        'featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'itinerary' => 'array',
            'price_from' => 'decimal:2',
            'featured' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

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
