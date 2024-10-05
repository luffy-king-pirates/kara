<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // JSON data
        $jsonData = '[
    {
        "role_name": "UpdateInvoice",
        "description": "update invoice process"
    },
    {
        "role_name": "UpdateTransfer",
        "description": "update transfer process"
    },
    {
        "role_name": "ApproveTransfer",
        "description": "Approve the transfer"
    },
    {
        "role_name": "ApproveInvoice",
        "description": "ApproveInvoice"
    },
    {
        "role_name": "TransferStock",
        "description": "perform stock transfer"
    },
    {
        "role_name": "SuperAdministrate",
        "description": "Perform full administration"
    },
    {
        "role_name": "Administrate",
        "description": "perform normal administaration"
    },
    {
        "role_name": "Sale",
        "description": "Perform sales"
    },
    {
        "role_name": "KeepStore",
        "description": "Perfom store keeping"
    },
    {
        "role_name": "ManageWarehouse",
        "description": "Manage all the ware house"
    },
    {
        "role_name": "SuperAdmin",
        "description": "control all the system"
    },
    {
        "role_name": "Admin",
        "description": "control administration"
    },
    {
        "role_name": "Sales",
        "description": "proces sales"
    },
    {
        "role_name": "WarehouseManager",
        "description": "manage stock transfers"
    },
    {
        "role_name": "StoreKeeper",
        "description": "manage stock transfers"
    }
]';

        // Decode the JSON data
        $items = json_decode($jsonData, true);

        // Insert each item into the database with created_by and updated_by set to null
        foreach ($items as $item) {
            Role::create([
                'role_name' => $item['role_name'],
                'description' => $item['description'],
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
