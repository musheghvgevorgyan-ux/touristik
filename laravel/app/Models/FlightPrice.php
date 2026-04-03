<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FlightPrice extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'from_city',
        'to_city',
        'price',
        'trip_type',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'updated_at' => 'datetime',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeForRoute(Builder $query, string $from, string $to): Builder
    {
        return $query->where('from_city', $from)->where('to_city', $to);
    }

    public function scopeRoundtrip(Builder $query): Builder
    {
        return $query->where('trip_type', 'roundtrip');
    }

    public function scopeOneway(Builder $query): Builder
    {
        return $query->where('trip_type', 'oneway');
    }
}
