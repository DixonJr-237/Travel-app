<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservations = [
            // Reservations for confirmed tickets
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Pending reservation
            [
                'date' => '2026-02-10',
                'status' => 'pending',
                'ticket_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // More confirmed reservations
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-10',
                'status' => 'confirmed',
                'ticket_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Cancelled reservation
            [
                'date' => '2026-02-10',
                'status' => 'cancelled',
                'ticket_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // More confirmed
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-02-11',
                'status' => 'confirmed',
                'ticket_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('reservations')->insert($reservations);
    }
}

