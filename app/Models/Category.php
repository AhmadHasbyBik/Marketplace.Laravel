<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class Category extends Model
{
    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (Route::has('categories.image')) {
            return route('categories.image', $this);
        }

        return Storage::disk('public')->url($this->image);
    }
}
