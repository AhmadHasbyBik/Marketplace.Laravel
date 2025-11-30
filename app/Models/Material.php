<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MaterialMovement;
use App\Models\Product;

class Material extends Model
{
    protected $guarded = [];

    public function movements(): HasMany
    {
        return $this->hasMany(MaterialMovement::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity')->withTimestamps();
    }
}
