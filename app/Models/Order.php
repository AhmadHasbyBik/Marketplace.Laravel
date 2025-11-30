<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShippingMethod;

class Order extends Model
{
    protected $guarded = [];

    public const TYPE_CUSTOMER = 'customer';
    public const TYPE_KASIR = 'kasir';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (! $this->payment_proof_path) {
            return null;
        }

        return url('storage/'.ltrim($this->payment_proof_path, '/'));
    }
}
