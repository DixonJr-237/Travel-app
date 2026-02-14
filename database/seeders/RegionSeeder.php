<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Morocco regions (country_id = 1)
        $regions = [
            // Morocco
            ['name' => 'Casablanca-Settat', 'id_country' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rabat-Salé-Kénitra', 'id_country' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marrakech-Safi', 'id_country' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Souss-Massa', 'id_country' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tanger-Tétouan-Al Hoceïma', 'id_country' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fès-Meknès', 'id_country' => 1, 'created_at' => now(), 'updated_at' => now()],
            // Spain (country_id = 2)
            ['name' => 'Andalusia', 'id_country' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Madrid', 'id_country' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Catalonia', 'id_country' => 2, 'created_at' => now(), 'updated_at' => now()],
            // France (country_id = 3)
            ['name' => 'Île-de-France', 'id_country' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Provence-Alpes-Côte d\'Azur', 'id_country' => 3, 'created_at' => now(), 'updated_at' => now()],
            // Germany (country_id = 4)
            ['name' => 'Berlin', 'id_country' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bavaria', 'id_country' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Portugal (country_id = 5)
            ['name' => 'Lisbon', 'id_country' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Porto', 'id_country' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('regions')->insert($regions);
    }
}

