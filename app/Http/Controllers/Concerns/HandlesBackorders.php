<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Product;
use App\Services\ProductionPlanner;

trait HandlesBackorders
{
    protected ProductionPlanner $productionPlanner;

    protected function describeBackorder(Product $product, int $totalQuantity): array
    {
        $total = max(0, $totalQuantity);
        $availableStock = max((int) $product->stock, 0);
        $reservedStock = min($availableStock, $total);
        $backorder = max(0, $total - $reservedStock);
        $requiresMaterials = $backorder > 0 && $product->materials->isNotEmpty();
        $productionReady = $backorder === 0 || $this->productionPlanner->canProduce($product, $backorder);

        return [
            'quantity' => $total,
            'reserved_stock' => $reservedStock,
            'backorder' => $backorder,
            'requires_materials' => $requiresMaterials,
            'production_ready' => $productionReady,
        ];
    }

    protected function formatCartPayload(Product $product, array $analysis): array
    {
        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $analysis['quantity'],
            'stock' => $product->stock,
            'backorder' => $analysis['backorder'],
            'reserved_stock' => $analysis['reserved_stock'],
            'requires_materials' => $analysis['requires_materials'],
            'production_ready' => $analysis['production_ready'],
        ];
    }
}
