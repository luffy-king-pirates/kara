<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Units; // Ensure this line is present
use App\Models\Item; // Ensure this line is present
use App\Models\Shops; // Ensure this line is present

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JSON data
          $jsonData = file_get_contents(database_path('seeders/data/shop.json'));

        // Decode the JSON data into an array
        $items = json_decode($jsonData, true);


        // Insert each item into the database with created_by and updated_by set to null
        foreach ($items as $item) {
            $unit = Units::where('unit_name', $item['unit_id'])->first();
            $itemdata = Item::where('item_code', $item['item_id'])->first();
            Shops::create([
                'item_id' => $itemdata ? $itemdata->id : null,
                'unit_id' =>$unit ? $unit->id : null,
                'quantity' => $item['quantity'] || 0 ,

            ]);
        }
    }
}
