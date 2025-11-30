<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialPurchaseItem extends Model
{
    protected $guarded = [];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(MaterialPurchase::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
