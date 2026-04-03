<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'reference',
        'supplier_ref',
        'user_id',
        'agency_id',
        'agent_id',
        'product_type',
        'supplier',
        'guest_first_name',
        'guest_last_name',
        'guest_email',
        'guest_phone',
        'product_data',
        'net_price',
        'sell_price',
        'commission',
        'currency',
        'promo_code_id',
        'discount_amount',
        'status',
        'payment_status',
        'supplier_request',
        'supplier_response',
    ];

    protected function casts(): array
    {
        return [
            'product_data' => 'array',
            'supplier_request' => 'array',
            'supplier_response' => 'array',
            'net_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'commission' => 'decimal:2',
            'discount_amount' => 'decimal:2',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAgency(Builder $query, int $agencyId): Builder
    {
        return $query->where('agency_id', $agencyId);
    }

    public function scopeRecentForUser(Builder $query, int $userId, int $limit = 10): Builder
    {
        return $query->where('user_id', $userId)->latest()->limit($limit);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // ─── Helper Methods ──────────────────────────────────────────

    public static function generateReference(): string
    {
        $prefix = 'TK';
        $datePart = now()->format('ymd');

        $lastToday = static::where('reference', 'like', "{$prefix}-{$datePart}-%")
            ->orderByDesc('reference')
            ->value('reference');

        if ($lastToday) {
            $lastSeq = (int) substr($lastToday, -3);
            $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '001';
        }

        return "{$prefix}-{$datePart}-{$nextSeq}";
    }

    public static function findByReference(string $reference): ?self
    {
        return static::where('reference', $reference)->first();
    }

    public function getGuestFullNameAttribute(): string
    {
        return trim("{$this->guest_first_name} {$this->guest_last_name}");
    }

    public function getMarginAttribute(): float
    {
        return (float) $this->sell_price - (float) $this->net_price;
    }
}
