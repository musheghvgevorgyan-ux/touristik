<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'transaction_id',
        'gateway',
        'amount',
        'currency',
        'method',
        'status',
        'gateway_response',
        'refund_amount',
        'refund_reason',
        'paid_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'gateway_response' => 'array',
            'amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeByGateway(Builder $query, string $gateway): Builder
    {
        return $query->where('gateway', $gateway);
    }
}
