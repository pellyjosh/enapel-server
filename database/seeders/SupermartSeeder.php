<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Api\Inventory;
use App\Models\SupermartCategory;
use App\Models\User;

class SupermartSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::first();
        if (!$staff) {
            $staff = User::create([
                'name' => 'Default Staff',
                'email' => 'staff@enapel.com',
                'password' => bcrypt('password'),
            ]);
        }

        $categories = [
            ['name' => 'Beverages', 'description' => 'Soft drinks, juices, and water'],
            ['name' => 'Grains', 'description' => 'Rice, beans, and other cereals'],
            ['name' => 'Toiletries', 'description' => 'Soap, toothpaste, and cleaning supplies'],
            ['name' => 'Snacks', 'description' => 'Biscuits, chips, and candies'],
            ['name' => 'Cooking Oil', 'description' => 'Vegetable oil, palm oil'],
        ];

        foreach ($categories as $cat) {
            SupermartCategory::firstOrCreate(['name' => $cat['name']], ['description' => $cat['description']]);
        }

        $products = [
            [
                'name' => 'Coca-Cola 50cl',
                'category' => 'Beverages',
                'description' => 'Refreshing soft drink',
                'quantity' => 120,
                'price' => 250.00,
            ],
            [
                'name' => 'Mama Gold Rice 5kg',
                'category' => 'Grains',
                'description' => 'Premium parboiled rice',
                'quantity' => 45,
                'price' => 8500.00,
            ],
            [
                'name' => 'Dettol Soap 100g',
                'category' => 'Toiletries',
                'description' => 'Antibacterial soap',
                'quantity' => 8, // Low stock
                'price' => 450.00,
            ],
            [
                'name' => 'Indomie Chicken Flavor (Pack)',
                'category' => 'Snacks',
                'description' => 'Delicious instant noodles',
                'quantity' => 0, // Out of stock
                'price' => 4500.00,
            ],
            [
                'name' => 'Kings Vegetable Oil 1L',
                'category' => 'Cooking Oil',
                'description' => 'Pure vegetable oil',
                'quantity' => 25,
                'price' => 2200.00,
            ],
        ];

        foreach ($products as $prod) {
            Inventory::create(array_merge($prod, [
                'staffid' => $staff->id,
            ]));
        }
    }
}
