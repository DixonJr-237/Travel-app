<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgencyActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            // EuroBus Casablanca activities
            [
                'id_company' => 1,
                'id_agence' => 1, // EuroBus Casablanca
                'id_region' => 1, // Casablanca-Settat
                'id_coord' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_company' => 1,
                'id_agence' => 1,
                'id_region' => 2, // Rabat-Salé-Kénitra
                'id_coord' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // EuroBus Rabat activities
            [
                'id_company' => 1,
                'id_agence' => 2, // EuroBus Rabat
                'id_region' => 2, // Rabat-Salé-Kénitra
                'id_coord' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_company' => 1,
                'id_agence' => 2,
                'id_region' => 6, // Fès-Meknès
                'id_coord' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech Express Marrakech activities
            [
                'id_company' => 2,
                'id_agence' => 3, // Marrakech Express Marrakech
                'id_region' => 3, // Marrakech-Safi
                'id_coord' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_company' => 2,
                'id_agence' => 3,
                'id_region' => 4, // Souss-Massa
                'id_coord' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Marrakech Express Agadir activities
            [
                'id_company' => 2,
                'id_agence' => 4, // Marrakech Express Agadir
                'id_region' => 4, // Souss-Massa
                'id_coord' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Iberia Travel Tanger activities
            [
                'id_company' => 3,
                'id_agence' => 5, // Iberia Travel Tanger
                'id_region' => 5, // Tanger-Tétouan
                'id_coord' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_company' => 3,
                'id_agence' => 5,
                'id_region' => 7, // Spain - Andalusia
                'id_coord' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Iberia Travel Fez activities
            [
                'id_company' => 3,
                'id_agence' => 6, // Iberia Travel Fez
                'id_region' => 6, // Fès-Meknès
                'id_coord' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_company' => 3,
                'id_agence' => 6,
                'id_region' => 3, // Marrakech-Safi
                'id_coord' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('agency_activities')->insert($activities);
    }
}

