<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Media;

class Banner extends Model
{
    protected $guarded = [];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
