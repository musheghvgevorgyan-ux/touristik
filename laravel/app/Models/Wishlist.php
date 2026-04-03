<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'item_data',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'item_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('item_type', $type);
    }
}
