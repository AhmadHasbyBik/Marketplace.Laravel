<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MaterialMovement;

class Material extends Model
{
    protected $guarded = [];

    public function movements(): HasMany
    {
        return $this->hasMany(MaterialMovement::class);
    }
}
