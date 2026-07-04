<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'product_id',
        'product_name',
        'product_slug',
        'customer_name',
        'customer_email',
        'amount',
        'currency',
        'status',
        'payment_method',
        'sslcz_tran_id',
        'sslcz_val_id',
        'license_key',
        'delivery_url',
        'provisioning_status',
        'provisioning_error',
        'paid_at',
        'delivered_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'delivered_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Create an order for a product, generating the sequential order number
     * (e.g. WM-2026-000042) atomically from the row id.
     */
    public static function place(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            $order = static::create($attributes + ['order_no' => 'pending']);
            $order->order_no = sprintf('WM-%s-%06d', now()->year, $order->id);
            $order->save();

            return $order;
        });
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function provisioningFailed(): bool
    {
        return $this->provisioning_status === 'failed';
    }

    /**
     * Downloads are available only while the order is delivered — a refund
     * revokes them.
     */
    public function allowsDownloads(): bool
    {
        return $this->isDelivered();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'delivered' => 'success',
            'paid' => 'pending',
            'pending' => 'pending',
            default => 'failed',
        };
    }
}
