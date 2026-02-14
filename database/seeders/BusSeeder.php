<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = [
            // EuroBus Casablanca (agency_id 1) - 3 buses
            [
                'registration_number' => 'EB-MA-1001',
                'seats_count' => 52,
                'model' => 'Mercedes-Benz Tourismo',
                'year' => 2022,
                'status' => 'active',
                'agency_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'EB-MA-1002',
                'seats_count' => 45,
                'model' => 'Volvo 9700',
                'year' => 2021,
                'status' => 'active',
                'agency_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'EB-MA-1003',
                'seats_count' => 50,
                'model' => 'Scania Interlink',
                'year' => 2020,
                'status' => 'maintenance',
                'agency_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // EuroBus Rabat (agency_id 2) - 2 buses
            [
                'registration_number' => 'EB-MA-2001',
                'seats_count' => 52,
                'model' => 'Mercedes-Benz Travego',
                'year' => 2023,
                'status' => 'active',
                'agency_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'EB-MA-2002',
                'seats_count' => 45,
                'model' => 'Setra S 415 HD',
                'year' => 2021,
                'status' => 'active',
                'agency_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech Express Marrakech (agency_id 3) - 3 buses
            [
                'registration_number' => 'ME-MA-3001',
                'seats_count' => 55,
                'model' => 'King Long XMQ6127',
                'year' => 2022,
                'status' => 'active',
                'agency_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'ME-MA-3002',
                'seats_count' => 48,
                'model' => 'Yutong ZK6129',
                'year' => 2021,
                'status' => 'active',
                'agency_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'ME-MA-3003',
                'seats_count' => 52,
                'model' => 'Higer KLQ6129',
                'year' => 2020,
                'status' => 'active',
                'agency_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech Express Agadir (agency_id 4) - 2 buses
            [
                'registration_number' => 'ME-MA-4001',
                'seats_count' => 50,
                'model' => 'Mercedes-Benz Tourismo',
                'year' => 2022,
                'status' => 'active',
                'agency_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'ME-MA-4002',
                'seats_count' => 45,
                'model' => 'Volvo 9700',
                'year' => 2021,
                'status' => 'active',
                'agency_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Iberia Travel Tanger (agency_id 5) - 2 buses
            [
                'registration_number' => 'IT-MA-5001',
                'seats_count' => 52,
                'model' => 'Mercedes-Benz Travego',
                'year' => 2023,
                'status' => 'active',
                'agency_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'IT-MA-5002',
                'seats_count' => 48,
                'model' => 'Scania Interlink',
                'year' => 2022,
                'status' => 'active',
                'agency_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Iberia Travel Fez (agency_id 6) - 2 buses
            [
                'registration_number' => 'IT-MA-6001',
                'seats_count' => 50,
                'model' => 'Setra S 415 HD',
                'year' => 2022,
                'status' => 'active',
                'agency_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'registration_number' => 'IT-MA-6002',
                'seats_count' => 45,
                'model' => 'Volvo 9700',
                'year' => 2020,
                'status' => 'active',
                'agency_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('buses')->insert($buses);
    }
}

