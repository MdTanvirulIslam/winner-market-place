<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'expires_at',
        'max_uses',
        'used_count',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expires_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }

    public function isRedeemable(): bool
    {
        return $this->active && ! $this->isExpired() && ! $this->isExhausted();
    }

    /**
     * The discount this coupon gives on a total — never more than the
     * total itself.
     */
    public function discountFor(float $amount): float
    {
        $discount = $this->type === 'percent'
            ? $amount * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $amount), 2);
    }

    /**
     * Why the coupon cannot be used right now, for customer-facing messages.
     */
    public function rejectionReason(): string
    {
        return match (true) {
            ! $this->active => 'This coupon is no longer active.',
            $this->isExpired() => 'This coupon has expired.',
            $this->isExhausted() => 'This coupon has reached its usage limit.',
            default => 'This coupon cannot be used.',
        };
    }

    public static function findByCode(?string $code): ?self
    {
        return $code ? static::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first() : null;
    }
}
