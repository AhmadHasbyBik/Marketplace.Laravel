<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Media;

class ProductImage extends Model
{
    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function getUrlAttribute(): string
    {
        $disk = $this->media?->disk ?? 'public';
        $path = $this->media?->path;

        if ($path && Storage::disk($disk)->exists($path)) {
            if ($disk === 'public') {
                return '/storage/' . ltrim($path, '/');
            }
            return Storage::disk($disk)->url($path);
        }

        return asset('images/product-placeholder.svg');
    }
}
