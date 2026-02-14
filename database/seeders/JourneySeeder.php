<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JourneySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $journeys = [
            // Casablanca routes
            [
                'name' => 'Casablanca to Rabat',
                'departure_location_coord_id' => 1, // Casablanca
                'arrival_location_coord_id' => 4,   // Rabat
                'distance' => 95.00,
                'estimated_duration' => 75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casablanca to Marrakech',
                'departure_location_coord_id' => 1, // Casablanca
                'arrival_location_coord_id' => 8,   // Marrakech
                'distance' => 240.00,
                'estimated_duration' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casablanca to Tanger',
                'departure_location_coord_id' => 1, // Casablanca
                'arrival_location_coord_id' => 14,  // Tanger
                'distance' => 250.00,
                'estimated_duration' => 210,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casablanca to Agadir',
                'departure_location_coord_id' => 1, // Casablanca
                'arrival_location_coord_id' => 11, // Agadir
                'distance' => 450.00,
                'estimated_duration' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Rabat routes
            [
                'name' => 'Rabat to Tanger',
                'departure_location_coord_id' => 4, // Rabat
                'arrival_location_coord_id' => 14, // Tanger
                'distance' => 230.00,
                'estimated_duration' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rabat to Fès',
                'departure_location_coord_id' => 4, // Rabat
                'arrival_location_coord_id' => 17, // Fès
                'distance' => 210.00,
                'estimated_duration' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rabat to Marrakech',
                'departure_location_coord_id' => 4, // Rabat
                'arrival_location_coord_id' => 8,   // Marrakech
                'distance' => 330.00,
                'estimated_duration' => 240,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech routes
            [
                'name' => 'Marrakech to Agadir',
                'departure_location_coord_id' => 8, // Marrakech
                'arrival_location_coord_id' => 11, // Agadir
                'distance' => 240.00,
                'estimated_duration' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marrakech to Essaouira',
                'departure_location_coord_id' => 8,  // Marrakech
                'arrival_location_coord_id' => 9,   // Essaouira
                'distance' => 170.00,
                'estimated_duration' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marrakech to Casablanca',
                'departure_location_coord_id' => 8, // Marrakech
                'arrival_location_coord_id' => 1,  // Casablanca
                'distance' => 240.00,
                'estimated_duration' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tanger routes
            [
                'name' => 'Tanger to Tétouan',
                'departure_location_coord_id' => 14, // Tanger
                'arrival_location_coord_id' => 15,  // Tétouan
                'distance' => 60.00,
                'estimated_duration' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tanger to Fès',
                'departure_location_coord_id' => 14, // Tanger
                'arrival_location_coord_id' => 17,  // Fès
                'distance' => 320.00,
                'estimated_duration' => 240,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tanger to Seville (Spain)',
                'departure_location_coord_id' => 14, // Tanger
                'arrival_location_coord_id' => 20,  // Seville
                'distance' => 250.00,
                'estimated_duration' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Fès routes
            [
                'name' => 'Fès to Meknès',
                'departure_location_coord_id' => 17, // Fès
                'arrival_location_coord_id' => 18,  // Meknès
                'distance' => 65.00,
                'estimated_duration' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fès to Marrakech',
                'departure_location_coord_id' => 17, // Fès
                'arrival_location_coord_id' => 8,   // Marrakech
                'distance' => 530.00,
                'estimated_duration' => 360,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Agadir routes
            [
                'name' => 'Agadir to Tiznit',
                'departure_location_coord_id' => 11, // Agadir
                'arrival_location_coord_id' => 13,   // Tiznit
                'distance' => 110.00,
                'estimated_duration' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agadir to Inezgane',
                'departure_location_coord_id' => 11, // Agadir
                'arrival_location_coord_id' => 12,   // Inezgane
                'distance' => 15.00,
                'estimated_duration' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // International routes
            [
                'name' => 'Tanger to Malaga',
                'departure_location_coord_id' => 14, // Tanger
                'arrival_location_coord_id' => 21,  // Malaga
                'distance' => 280.00,
                'estimated_duration' => 330,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casablanca to Madrid',
                'departure_location_coord_id' => 1,  // Casablanca
                'arrival_location_coord_id' => 22,  // Madrid
                'distance' => 850.00,
                'estimated_duration' => 720,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marrakech to Paris',
                'departure_location_coord_id' => 8,  // Marrakech
                'arrival_location_coord_id' => 24,  // Paris
                'distance' => 1900.00,
                'estimated_duration' => 1440,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('journeys')->insert($journeys);
    }
}

