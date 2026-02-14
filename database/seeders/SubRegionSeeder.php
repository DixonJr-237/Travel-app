<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Morocco sub-regions
        $subRegions = [
            // Casablanca-Settat (region_id 1)
            ['name' => 'Casablanca', 'id_region' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mohammedia', 'id_region' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'El Jadida', 'id_region' => 1, 'created_at' => now(), 'updated_at' => now()],
            // Rabat-Salé-Kénitra (region_id 2)
            ['name' => 'Rabat', 'id_region' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salé', 'id_region' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Témara', 'id_region' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kénitra', 'id_region' => 2, 'created_at' => now(), 'updated_at' => now()],
            // Marrakech-Safi (region_id 3)
            ['name' => 'Marrakech', 'id_region' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Essaouira', 'id_region' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Safi', 'id_region' => 3, 'created_at' => now(), 'updated_at' => now()],
            // Souss-Massa (region_id 4)
            ['name' => 'Agadir', 'id_region' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inezgane', 'id_region' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tiznit', 'id_region' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Tanger-Tétouan (region_id 5)
            ['name' => 'Tanger', 'id_region' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tétouan', 'id_region' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fnideq', 'id_region' => 5, 'created_at' => now(), 'updated_at' => now()],
            // Fès-Meknès (region_id 6)
            ['name' => 'Fès', 'id_region' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meknès', 'id_region' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ifrane', 'id_region' => 6, 'created_at' => now(), 'updated_at' => now()],
            // Spain regions
            ['name' => 'Seville', 'id_region' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Malaga', 'id_region' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Madrid City', 'id_region' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Barcelona', 'id_region' => 9, 'created_at' => now(), 'updated_at' => now()],
            // France
            ['name' => 'Paris', 'id_region' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marseille', 'id_region' => 11, 'created_at' => now(), 'updated_at' => now()],
            // Germany
            ['name' => 'Berlin City', 'id_region' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Munich', 'id_region' => 13, 'created_at' => now(), 'updated_at' => now()],
            // Portugal
            ['name' => 'Lisbon City', 'id_region' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Porto City', 'id_region' => 15, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('sub_regions')->insert($subRegions);
    }
}

