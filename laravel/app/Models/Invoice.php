<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'invoice_no',
        'agency_id',
        'booking_id',
        'amount',
        'currency',
        'status',
        'due_date',
        'paid_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['sent', 'overdue']);
    }

    public function scopeForAgency(Builder $query, int $agencyId): Builder
    {
        return $query->where('agency_id', $agencyId);
    }
}
