<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PosTransaction;
use App\Models\Product;

class PosTransactionItem extends Model
{
    protected $guarded = [];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
