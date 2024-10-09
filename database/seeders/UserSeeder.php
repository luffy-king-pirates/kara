<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Ensure this line is present

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JSON data
        $jsonData = '[
    {
        "name": "Ashok Hindocha",
        "phone": "0692058928",
        "first_name": "Hindocha",
        "last_name": "Ashok",
        "last_login": null,
        "last_logout": null,
        "email": "Ashok Hindocha@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Athman Mohamed  Athman",
        "phone": "0694322636",
        "first_name": "Mohamed",
        "last_name": "Athman",
        "last_login": null,
        "last_logout": null,
        "email": "Athman Mohamed  Athman@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Geofrey Phocus Nshoki",
        "phone": "0713849122",
        "first_name": "Phocus",
        "last_name": "Geofrey",
        "last_login": null,
        "last_logout": null,
        "email": "Geofrey Phocus Nshoki@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Jagdish Dauda",
        "phone": "255 717 317 171",
        "first_name": "Dauda",
        "last_name": "Jagdish",
        "last_login": null,
        "last_logout": null,
        "email": "Jagdish Dauda@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Hamis Majengo",
        "phone": "8767878576",
        "first_name": "Majengo",
        "last_name": "Hamis",
        "last_login": null,
        "last_logout": null,
        "email": "Hamis Majengo@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Anuj Kara",
        "phone": "0689595478",
        "first_name": "Kara",
        "last_name": "Anuj",
        "last_login": null,
        "last_logout": null,
        "email": "Anuj Kara@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Dhiren Bachu",
        "phone": "866",
        "first_name": "Bachu",
        "last_name": "Dhiren",
        "last_login": null,
        "last_logout": null,
        "email": "Dhiren Bachu@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Kamila Shantilal  Gondalia",
        "phone": "078431263",
        "first_name": "Shantilal",
        "last_name": "Kamila",
        "last_login": null,
        "last_logout": null,
        "email": "Kamila Shantilal  Gondalia@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Pratish M Kara",
        "phone": "0713326311",
        "first_name": "M",
        "last_name": "Pratish",
        "last_login": null,
        "last_logout": null,
        "email": "Pratish M Kara@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Developer Team",
        "phone": "+919841277223",
        "first_name": "Team",
        "last_name": "Developer",
        "last_login": null,
        "last_logout": null,
        "email": "Developer Team@gmail.com",
        "password": "Kara"
    },
    {
        "name": "RAJESH CHAUDHARY",
        "phone": "0786160130",
        "first_name": "CHAUDHARY",
        "last_name": "RAJESH",
        "last_login": null,
        "last_logout": null,
        "email": "RAJESH CHAUDHARY@gmail.com",
        "password": "Kara"
    },
    {
        "name": "Jane Monge",
        "phone": "785",
        "first_name": "Monge",
        "last_name": "Jane",
        "last_login": null,
        "last_logout": null,
        "email": "Jane Monge@gmail.com",
        "password": "Kara"
    }
]';

        // Decode the JSON data
        $items = json_decode($jsonData, true);

        // Insert each item into the database with created_by and updated_by set to null
        foreach ($items as $item) {
            User::create([
                'name' => $item['name'],
                'phone' => $item['phone'],
                'first_name' => $item['phone'],
                'last_name' => $item['phone'],
                'last_login' => $item['last_login'],
                'last_logout' => $item['last_logout'],
                'password' => $item['password'],
                'email'=> $item['email']
            ]);
        }
    }
}
