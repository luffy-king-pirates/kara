<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Units; // Ensure this line is present
use App\Models\Item; // Ensure this line is present

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JSON data
          $jsonData = file_get_contents(database_path('seeders/data/items.json'));

        // Decode the JSON data into an array
        $items = json_decode($jsonData, true);
        

        // Insert each item into the database with created_by and updated_by set to null
        foreach ($items as $item) {
            $unit = Units::where('unit_name', $item['unit_id'])->first();
            Item::create([
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'item_category' => null,
                'item_brand' => null,
                'item_size' => $item['specification'],
                'item_unit' => $unit ? $unit->id : null,
                'mfg_code' => $item["mfg_code"],
                "inclusive" =>'',
                "exclusive" => '',
                "item_description" => $item["specification"],
                "specification" => $item["specification"],
                'image_url' => $item["image_url"]
            ]);
        }
    }
}
