<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Material;
use App\Models\User;

class MaterialMovement extends Model
{
    protected $guarded = [];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
