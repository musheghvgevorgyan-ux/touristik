<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoUsage extends Model
{
    public $timestamps = false;

    protected $table = 'promo_usage';

    protected $fillable = [
        'promo_id',
        'user_id',
        'booking_id',
        'discount_amount',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'promo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
