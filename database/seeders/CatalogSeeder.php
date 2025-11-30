<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Makanan Ringan', 'slug' => 'makanan-ringan', 'description' => 'Sneak dan kudapan manis.', 'order' => 1],
            ['name' => 'Minuman Segar', 'slug' => 'minuman-segar', 'description' => 'Es kopyor & minuman signature.', 'order' => 2],
            ['name' => 'Paket Hampers', 'slug' => 'paket-hampers', 'description' => 'Box cantik untuk hadiah.', 'order' => 3],
        ]);

        $categories->each(function ($category) {
            Category::updateOrCreate(['slug' => $category['slug']], array_merge($category, ['is_active' => true]));
        });

        $products = [
            [
                'name' => 'Kue Nastar Cokelat',
                'slug' => 'kue-nastar-cokelat',
                'category_slug' => 'makanan-ringan',
                'price' => 25000,
                'stock' => 80,
                'short_description' => 'Crispy pastry dengan isi selai cokelat premium.',
                'is_featured' => true,
            ],
            [
                'name' => 'Es Kopyor Betawi',
                'slug' => 'es-kopyor-betawi',
                'category_slug' => 'minuman-segar',
                'price' => 33000,
                'stock' => 120,
                'short_description' => 'Perpaduan kelapa muda dan susu kental manis.',
                'is_featured' => true,
            ],
            [
                'name' => 'Paket Kado Dapoer Cupid',
                'slug' => 'paket-kado-dapoer',
                'category_slug' => 'paket-hampers',
                'price' => 150000,
                'stock' => 20,
                'short_description' => 'Box premium untuk momen istimewa.',
            ],
        ];

        foreach ($products as $payload) {
            $category = Category::where('slug', $payload['category_slug'])->first();
            if (! $category) {
                continue;
            }

            $data = $payload;
            unset($data['category_slug']);

            Product::updateOrCreate(
                ['slug' => $payload['slug']],
                array_merge($data, [
                    'category_id' => $category->id,
                    'cost_price' => $payload['price'] * 0.6,
                    'stock_minimum' => 5,
                    'is_active' => true,
                ])
            );
        }
    }
}
