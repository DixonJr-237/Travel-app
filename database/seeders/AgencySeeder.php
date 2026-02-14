<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            // EuroBus International agencies (company_id = 1)
            [
                'name' => 'EuroBus Casablanca Central',
                'phone' => '+212522100001',
                'email' => 'casablanca@eurobus.com',
                'user_id' => 5, // eurobus_casablanca
                'id_company' => 1,
                'id_coord' => 1, // Casa Voyageurs
                'id_city' => 1,   // Casablanca
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'EuroBus Rabat Express',
                'phone' => '+212537100002',
                'email' => 'rabat@eurobus.com',
                'user_id' => 6, // eurobus_rabat
                'id_company' => 1,
                'id_coord' => 4, // Rabat
                'id_city' => 4,  // Rabat
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Marrakech Express agencies (company_id = 2)
            [
                'name' => 'Marrakech Express Marrakech',
                'phone' => '+212524400001',
                'email' => 'marrakech@marrakechexpress.com',
                'user_id' => 7, // marrakechexpress_marrakech
                'id_company' => 2,
                'id_coord' => 8, // Marrakech
                'id_city' => 8,  // Marrakech
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marrakech Express Agadir',
                'phone' => '+212528400002',
                'email' => 'agadir@marrakechexpress.com',
                'user_id' => 8, // marrakechexpress_agadir
                'id_company' => 2,
                'id_coord' => 11, // Agadir
                'id_city' => 11,  // Agadir
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Iberia Travel agencies (company_id = 3)
            [
                'name' => 'Iberia Travel Tanger',
                'phone' => '+212539500001',
                'email' => 'tanger@iberiatravel.com',
                'user_id' => 9, // iberiatravel_tangier
                'id_company' => 3,
                'id_coord' => 14, // Tanger
                'id_city' => 14,  // Tanger
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Iberia Travel Fez',
                'phone' => '+212535500002',
                'email' => 'fez@iberiatravel.com',
                'user_id' => 10, // iberiatravel_fez
                'id_company' => 3,
                'id_coord' => 17, // Fès
                'id_city' => 17,  // Fès
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('agencies')->insert($agencies);
    }
}

