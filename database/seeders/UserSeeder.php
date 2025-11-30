<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Address;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $kasirRole = Role::where('name', 'kasir')->first();
        $customerRole = Role::where('name', 'customer')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@dapoercupid.test'],
            [
                'name' => 'Admin Dapoer Cupid',
                'phone' => '081234500001',
                'password' => bcrypt('password'),
                'is_active' => true,
                'is_verified' => true,
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        $kasir = User::updateOrCreate(
            ['email' => 'kasir@dapoercupid.test'],
            [
                'name' => 'Kasir Dapoer Cupid',
                'phone' => '081234500002',
                'password' => bcrypt('password'),
                'is_active' => true,
                'is_verified' => true,
            ]
        );
        $kasir->roles()->sync([$kasirRole->id]);

        $customer = User::updateOrCreate(
            ['email' => 'customer@dapoercupid.test'],
            [
                'name' => 'Customer Dapoer Cupid',
                'phone' => '081234500003',
                'password' => bcrypt('password'),
                'is_active' => true,
                'is_verified' => true,
            ]
        );
        $customer->roles()->sync([$customerRole->id]);

        Address::firstOrCreate([
            'user_id' => $customer->id,
            'recipient_name' => 'Customer Dapoer',
            'street' => 'Jl. Ceria No.10',
            'city' => 'Jakarta Selatan',
            'phone' => $customer->phone,
        ]);
    }
}
