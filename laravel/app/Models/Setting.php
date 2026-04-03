<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    // ─── Helper Methods ──────────────────────────────────────────

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('setting_key', $key)->first();

        return $setting ? $setting->setting_value : $default;
    }

    public static function setValue(string $key, ?string $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            array_filter([
                'setting_value' => $value,
                'description' => $description,
                'updated_at' => now(),
            ], fn ($v) => $v !== null)
        );
    }
}
