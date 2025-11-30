<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'JNE Reguler',
                'slug' => 'jne',
                'description' => 'Tarif tetap JNE Reguler.',
                'type' => 'courier',
                'flat_rate' => 25000,
                'estimation' => '2-4 hari',
                'is_active' => true,
            ],
            [
                'name' => 'POS Indonesia',
                'slug' => 'pos',
                'description' => 'Layanan POS Indonesia Reguler.',
                'type' => 'courier',
                'flat_rate' => 22000,
                'estimation' => '3-5 hari',
                'is_active' => true,
            ],
            [
                'name' => 'TIKI Reguler',
                'slug' => 'tiki',
                'description' => 'Tarif standar TIKI Reguler.',
                'type' => 'courier',
                'flat_rate' => 23000,
                'estimation' => '2-4 hari',
                'is_active' => true,
            ],
            [
                'name' => 'Ambil di Toko',
                'slug' => 'pickup',
                'description' => 'Ambil langsung pesanan di toko.',
                'type' => 'pickup',
                'flat_rate' => 0,
                'estimation' => 'Siap dalam 1 hari',
                'is_active' => true,
            ],
        ];

        foreach ($methods as $payload) {
            ShippingMethod::updateOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );
        }
    }
}
