<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            // Ticket 1 - Mohammed Benali (customer_id 1) - Casablanca to Rabat
            [
                'purchase_date' => '2026-02-10 10:30:00',
                'price' => 80.00,
                'status' => 'confirmed',
                'seat_number' => 'A1',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 1,
                'customer_id' => 1,
                'trip_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 2 - Fatima Zahra (customer_id 2) - Casablanca to Rabat
            [
                'purchase_date' => '2026-02-10 11:00:00',
                'price' => 80.00,
                'status' => 'confirmed',
                'seat_number' => 'A2',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 1,
                'customer_id' => 2,
                'trip_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 3 - Ahmed Kadiri (customer_id 3) - Casablanca to Marrakech
            [
                'purchase_date' => '2026-02-10 12:00:00',
                'price' => 150.00,
                'status' => 'confirmed',
                'seat_number' => 'B1',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 2,
                'customer_id' => 3,
                'trip_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 4 - Aicha Mourad (customer_id 4) - Casablanca to Marrakech
            [
                'purchase_date' => '2026-02-10 13:30:00',
                'price' => 130.00,
                'status' => 'confirmed',
                'seat_number' => 'B2',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 2,
                'customer_id' => 4,
                'trip_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 5 - Youssef Amrani (customer_id 5) - Rabat to Tanger
            [
                'purchase_date' => '2026-02-10 14:00:00',
                'price' => 140.00,
                'status' => 'confirmed',
                'seat_number' => 'C1',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 5,
                'customer_id' => 5,
                'trip_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 6 - Samira El Amrani (customer_id 6) - Marrakech to Agadir
            [
                'purchase_date' => '2026-02-10 15:00:00',
                'price' => 120.00,
                'status' => 'confirmed',
                'seat_number' => 'D1',
                'booking_reference' => 'ME' . strtoupper(uniqid()),
                'journey_id' => 8,
                'customer_id' => 6,
                'trip_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 7 - Rachid Belhaj (customer_id 7) - Marrakech to Agadir
            [
                'purchase_date' => '2026-02-10 15:30:00',
                'price' => 120.00,
                'status' => 'pending',
                'seat_number' => 'D2',
                'booking_reference' => 'ME' . strtoupper(uniqid()),
                'journey_id' => 8,
                'customer_id' => 7,
                'trip_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 8 - Nadia Chakir (customer_id 8) - Tanger to Fès
            [
                'purchase_date' => '2026-02-10 16:00:00',
                'price' => 180.00,
                'status' => 'confirmed',
                'seat_number' => 'E1',
                'booking_reference' => 'IT' . strtoupper(uniqid()),
                'journey_id' => 12,
                'customer_id' => 8,
                'trip_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 9 - Omar Idrissi (customer_id 9) - Fès to Meknès
            [
                'purchase_date' => '2026-02-10 16:30:00',
                'price' => 50.00,
                'status' => 'confirmed',
                'seat_number' => 'F1',
                'booking_reference' => 'IT' . strtoupper(uniqid()),
                'journey_id' => 14,
                'customer_id' => 9,
                'trip_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 10 - Laila Bennis (customer_id 10) - Tanger to Seville
            [
                'purchase_date' => '2026-02-10 17:00:00',
                'price' => 45.00,
                'status' => 'confirmed',
                'seat_number' => 'G1',
                'booking_reference' => 'IT' . strtoupper(uniqid()),
                'journey_id' => 13,
                'customer_id' => 10,
                'trip_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 11 - Hicham Faouzi (customer_id 11) - Marrakech to Essaouira
            [
                'purchase_date' => '2026-02-10 17:30:00',
                'price' => 80.00,
                'status' => 'confirmed',
                'seat_number' => 'H1',
                'booking_reference' => 'ME' . strtoupper(uniqid()),
                'journey_id' => 9,
                'customer_id' => 11,
                'trip_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 12 - Souad Mekki (customer_id 12) - Rabat to Fès
            [
                'purchase_date' => '2026-02-10 18:00:00',
                'price' => 130.00,
                'status' => 'cancelled',
                'seat_number' => 'I1',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 6,
                'customer_id' => 12,
                'trip_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 13 - Karim Bouazza (customer_id 13) - Casablanca to Tanger
            [
                'purchase_date' => '2026-02-11 09:00:00',
                'price' => 160.00,
                'status' => 'confirmed',
                'seat_number' => 'J1',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 3,
                'customer_id' => 13,
                'trip_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 14 - Amina Tazi (customer_id 14) - Casablanca to Rabat
            [
                'purchase_date' => '2026-02-11 10:00:00',
                'price' => 75.00,
                'status' => 'confirmed',
                'seat_number' => 'A3',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 1,
                'customer_id' => 14,
                'trip_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 15 - Abdelkrim Hajji (customer_id 15) - Marrakech to Agadir
            [
                'purchase_date' => '2026-02-11 11:00:00',
                'price' => 115.00,
                'status' => 'confirmed',
                'seat_number' => 'D3',
                'booking_reference' => 'ME' . strtoupper(uniqid()),
                'journey_id' => 8,
                'customer_id' => 15,
                'trip_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 16 - Halima Essafi (customer_id 16) - Tanger to Fès
            [
                'purchase_date' => '2026-02-11 12:00:00',
                'price' => 180.00,
                'status' => 'used',
                'seat_number' => 'E2',
                'booking_reference' => 'IT' . strtoupper(uniqid()),
                'journey_id' => 12,
                'customer_id' => 16,
                'trip_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 17 - Tarik Mouhcine (customer_id 17) - Marrakech to Essaouira
            [
                'purchase_date' => '2026-02-11 13:00:00',
                'price' => 75.00,
                'status' => 'confirmed',
                'seat_number' => 'H2',
                'booking_reference' => 'ME' . strtoupper(uniqid()),
                'journey_id' => 9,
                'customer_id' => 17,
                'trip_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 18 - Asmaa Rhazi (customer_id 18) - Casablanca to Marrakech
            [
                'purchase_date' => '2026-02-11 14:00:00',
                'price' => 150.00,
                'status' => 'confirmed',
                'seat_number' => 'B3',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 2,
                'customer_id' => 18,
                'trip_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 19 - Anas Sqalli (customer_id 19) - Rabat to Tanger
            [
                'purchase_date' => '2026-02-11 15:00:00',
                'price' => 140.00,
                'status' => 'confirmed',
                'seat_number' => 'C2',
                'booking_reference' => 'EB' . strtoupper(uniqid()),
                'journey_id' => 5,
                'customer_id' => 19,
                'trip_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ticket 20 - Rania Lahlou (customer_id 20) - Fès to Meknès
            [
                'purchase_date' => '2026-02-11 16:00:00',
                'price' => 50.00,
                'status' => 'confirmed',
                'seat_number' => 'F2',
                'booking_reference' => 'IT' . strtoupper(uniqid()),
                'journey_id' => 14,
                'customer_id' => 20,
                'trip_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tickets')->insert($tickets);
    }
}

