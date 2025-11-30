<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductionPlanner
{
    public function requiredMaterials(Product $product, int $units): Collection
    {
        if ($units <= 0) {
            return collect();
        }

        $product->loadMissing('materials');

        return $product->materials->map(function ($material) use ($units) {
            $unitQuantity = (float) ($material->pivot->quantity ?? 0);

            return [
                'material' => $material,
                'unit_quantity' => $unitQuantity,
                'needed' => $unitQuantity * $units,
            ];
        })->filter(fn ($entry) => $entry['unit_quantity'] > 0);
    }

    public function canProduce(Product $product, int $units): bool
    {
        $required = $this->requiredMaterials($product, $units);

        if ($required->isEmpty()) {
            return false;
        }

        return $required->every(fn ($entry) => $entry['needed'] <= $entry['material']->stock);
    }
}
