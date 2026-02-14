<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Morocco - Casablanca-Settat (sub_region_id 1-3)
            ['name' => 'Casablanca', 'id_sub_region' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mohammedia', 'id_sub_region' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'El Jadida', 'id_sub_region' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Rabat-Salé-Kénitra (sub_region_id 4-7)
            ['name' => 'Rabat', 'id_sub_region' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salé', 'id_sub_region' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Témara', 'id_sub_region' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kénitra', 'id_sub_region' => 7, 'created_at' => now(), 'updated_at' => now()],

            // Marrakech-Safi (sub_region_id 8-10)
            ['name' => 'Marrakech', 'id_sub_region' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Essaouira', 'id_sub_region' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Safi', 'id_sub_region' => 10, 'created_at' => now(), 'updated_at' => now()],

            // Souss-Massa (sub_region_id 11-13)
            ['name' => 'Agadir', 'id_sub_region' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inezgane', 'id_sub_region' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tiznit', 'id_sub_region' => 13, 'created_at' => now(), 'updated_at' => now()],

            // Tanger-Tétouan (sub_region_id 14-16)
            ['name' => 'Tanger', 'id_sub_region' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tétouan', 'id_sub_region' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fnideq', 'id_sub_region' => 16, 'created_at' => now(), 'updated_at' => now()],

            // Fès-Meknès (sub_region_id 17-19)
            ['name' => 'Fès', 'id_sub_region' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meknès', 'id_sub_region' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ifrane', 'id_sub_region' => 19, 'created_at' => now(), 'updated_at' => now()],

            // Spain (sub_region_id 20-23)
            ['name' => 'Seville', 'id_sub_region' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Malaga', 'id_sub_region' => 21, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Madrid', 'id_sub_region' => 22, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barcelona', 'id_sub_region' => 23, 'created_at' => now(), 'updated_at' => now()],

            // France (sub_region_id 24-25)
            ['name' => 'Paris', 'id_sub_region' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marseille', 'id_sub_region' => 25, 'created_at' => now(), 'updated_at' => now()],

            // Germany (sub_region_id 26-27)
            ['name' => 'Berlin', 'id_sub_region' => 26, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Munich', 'id_sub_region' => 27, 'created_at' => now(), 'updated_at' => now()],

            // Portugal (sub_region_id 28-29)
            ['name' => 'Lisbon', 'id_sub_region' => 28, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Porto', 'id_sub_region' => 29, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('cities')->insert($cities);
    }
}

