<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Units; // Ensure this line is present

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // JSON data
        $jsonData = '[
    {
        "unit_name": "PCS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "SETS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "PKTS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "STAND",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "PAIRS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "TINS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "GALLON",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "GROSS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "OUTERS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "ROLLS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "KTS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "KGS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "BOXES",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "BUNDLES",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "MTRS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "TABLETS",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "Bags",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "buckets",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "Bottle",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "CTN",
        "created_by": 1,
        "updated_by": 1
    },
    {
        "unit_name": "Ltrs",
        "created_by": 1,
        "updated_by": 1
    }
]';

        // Decode the JSON data
        $items = json_decode($jsonData, true);

        // Insert each item into the database with created_by and updated_by set to null
        foreach ($items as $item) {
            Units::create([
                'unit_name' => $item['unit_name'],
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
