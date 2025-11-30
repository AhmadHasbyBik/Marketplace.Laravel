<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialPurchase extends Model
{
    protected $guarded = [];

    protected $casts = [
        'purchase_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MaterialPurchaseItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MaterialPurchaseTransaction::class);
    }

    public function getPaidAmountAttribute(): float
    {
        if (array_key_exists('transactions_sum_amount', $this->attributes)) {
            return (float) $this->attributes['transactions_sum_amount'];
        }

        return $this->transactions->sum('amount') ?? 0;
    }

    public function getBalanceAttribute(): float
    {
        return max($this->total - $this->paid_amount, 0);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
