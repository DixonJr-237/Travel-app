<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tips = [
            // Casablanca to Rabat trips (journey_id 1)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '08:00:00',
                'initial_price' => 80.00,
                'available_seats' => 45,
                'status' => 'scheduled',
                'bus_id' => 1,
                'journey_id' => 1,
                'departure_location_coord_id' => 1,
                'arrival_location_coord_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '14:00:00',
                'initial_price' => 80.00,
                'available_seats' => 50,
                'status' => 'scheduled',
                'bus_id' => 2,
                'journey_id' => 1,
                'departure_location_coord_id' => 1,
                'arrival_location_coord_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '09:00:00',
                'initial_price' => 75.00,
                'available_seats' => 52,
                'status' => 'scheduled',
                'bus_id' => 4,
                'journey_id' => 1,
                'departure_location_coord_id' => 4,
                'arrival_location_coord_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Casablanca to Marrakech trips (journey_id 2)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '07:00:00',
                'initial_price' => 150.00,
                'available_seats' => 48,
                'status' => 'scheduled',
                'bus_id' => 2,
                'journey_id' => 2,
                'departure_location_coord_id' => 1,
                'arrival_location_coord_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '21:00:00',
                'initial_price' => 130.00,
                'available_seats' => 52,
                'status' => 'scheduled',
                'bus_id' => 1,
                'journey_id' => 2,
                'departure_location_coord_id' => 1,
                'arrival_location_coord_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '08:00:00',
                'initial_price' => 150.00,
                'available_seats' => 50,
                'status' => 'scheduled',
                'bus_id' => 5,
                'journey_id' => 2,
                'departure_location_coord_id' => 4,
                'arrival_location_coord_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Rabat to Tanger trips (journey_id 5)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '06:30:00',
                'initial_price' => 140.00,
                'available_seats' => 45,
                'status' => 'scheduled',
                'bus_id' => 4,
                'journey_id' => 5,
                'departure_location_coord_id' => 4,
                'arrival_location_coord_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '07:00:00',
                'initial_price' => 140.00,
                'available_seats' => 50,
                'status' => 'scheduled',
                'bus_id' => 5,
                'journey_id' => 5,
                'departure_location_coord_id' => 4,
                'arrival_location_coord_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech to Agadir trips (journey_id 8)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '08:00:00',
                'initial_price' => 120.00,
                'available_seats' => 50,
                'status' => 'scheduled',
                'bus_id' => 6,
                'journey_id' => 8,
                'departure_location_coord_id' => 8,
                'arrival_location_coord_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '14:00:00',
                'initial_price' => 120.00,
                'available_seats' => 45,
                'status' => 'scheduled',
                'bus_id' => 7,
                'journey_id' => 8,
                'departure_location_coord_id' => 8,
                'arrival_location_coord_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '09:00:00',
                'initial_price' => 115.00,
                'available_seats' => 48,
                'status' => 'scheduled',
                'bus_id' => 9,
                'journey_id' => 8,
                'departure_location_coord_id' => 11,
                'arrival_location_coord_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tanger to Fès trips (journey_id 12)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '07:00:00',
                'initial_price' => 180.00,
                'available_seats' => 48,
                'status' => 'scheduled',
                'bus_id' => 11,
                'journey_id' => 12,
                'departure_location_coord_id' => 14,
                'arrival_location_coord_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '08:00:00',
                'initial_price' => 180.00,
                'available_seats' => 45,
                'status' => 'scheduled',
                'bus_id' => 13,
                'journey_id' => 12,
                'departure_location_coord_id' => 17,
                'arrival_location_coord_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Fès to Meknès trips (journey_id 14)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '09:00:00',
                'initial_price' => 50.00,
                'available_seats' => 50,
                'status' => 'scheduled',
                'bus_id' => 13,
                'journey_id' => 14,
                'departure_location_coord_id' => 17,
                'arrival_location_coord_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '15:00:00',
                'initial_price' => 50.00,
                'available_seats' => 45,
                'status' => 'scheduled',
                'bus_id' => 14,
                'journey_id' => 14,
                'departure_location_coord_id' => 18,
                'arrival_location_coord_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tanger to Seville (Spain) trips (journey_id 13)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '08:00:00',
                'initial_price' => 45.00,
                'available_seats' => 48,
                'status' => 'scheduled',
                'bus_id' => 11,
                'journey_id' => 13,
                'departure_location_coord_id' => 14,
                'arrival_location_coord_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech to Essaouira trips (journey_id 9)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '08:30:00',
                'initial_price' => 80.00,
                'available_seats' => 52,
                'status' => 'scheduled',
                'bus_id' => 8,
                'journey_id' => 9,
                'departure_location_coord_id' => 8,
                'arrival_location_coord_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '09:00:00',
                'initial_price' => 75.00,
                'available_seats' => 50,
                'status' => 'scheduled',
                'bus_id' => 6,
                'journey_id' => 9,
                'departure_location_coord_id' => 9,
                'arrival_location_coord_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Rabat to Fès trips (journey_id 6)
            [
                'departure_date' => '2026-02-15',
                'departure_time' => '06:00:00',
                'initial_price' => 130.00,
                'available_seats' => 52,
                'status' => 'scheduled',
                'bus_id' => 5,
                'journey_id' => 6,
                'departure_location_coord_id' => 4,
                'arrival_location_coord_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Casablanca to Tanger trips (journey_id 3)
            [
                'departure_date' => '2026-02-16',
                'departure_time' => '07:00:00',
                'initial_price' => 160.00,
                'available_seats' => 45,
                'status' => 'scheduled',
                'bus_id' => 2,
                'journey_id' => 3,
                'departure_location_coord_id' => 1,
                'arrival_location_coord_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tips')->insert($tips);
    }
}

