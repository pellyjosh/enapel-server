<?php

namespace Database\Seeders;

use App\Models\RoomCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['name' => 'Standard', 'description' => 'Basic room with essential amenities.', 'base_price' => 50.00],
            ['name' => 'Deluxe', 'description' => 'Spacious room with premium features.', 'base_price' => 100.00],
            ['name' => 'Suite', 'description' => 'Luxury suite with exclusive services.', 'base_price' => 200.00],
            ['name' => 'Penthouse', 'description' => 'Top-floor suite with the best amenities.', 'base_price' => 500.00],
        ];

        foreach ($categories as $category) {
            RoomCategory::create($category);
        }
    }
}
