<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = RoomCategory::all();

        $rooms = [
            ['name' => 'Room 101', 'category_id' => $categories->where('name', 'Standard')->first()->id, 'status' => 'available', 'price' => 55.00, 'is_clean' => true],
            ['name' => 'Room 102', 'category_id' => $categories->where('name', 'Standard')->first()->id, 'status' => 'occupied', 'price' => 55.00, 'is_clean' => false],
            ['name' => 'Room 201', 'category_id' => $categories->where('name', 'Deluxe')->first()->id, 'status' => 'available', 'price' => 110.00, 'is_clean' => true],
            ['name' => 'Room 202', 'category_id' => $categories->where('name', 'Deluxe')->first()->id, 'status' => 'maintenance', 'price' => 110.00, 'is_clean' => false],
            ['name' => 'Room 301', 'category_id' => $categories->where('name', 'Suite')->first()->id, 'status' => 'available', 'price' => 210.00, 'is_clean' => true],
            ['name' => 'Room 401', 'category_id' => $categories->where('name', 'Penthouse')->first()->id, 'status' => 'available', 'price' => 510.00, 'is_clean' => true],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
