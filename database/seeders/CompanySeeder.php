<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get user IDs for company admins (user_id 2,3,4 based on UserSeeder)
        // user_id 1 = super admin, 2 = EuroBus, 3 = Marrakech Express, 4 = Iberia Travel

        $companies = [
            [
                'name' => 'EuroBus International',
                'phone' => '+212522000001',
                'email' => 'contact@eurobus.com',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marrakech Express',
                'phone' => '+212524000002',
                'email' => 'contact@marrakechexpress.com',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Iberia Travel',
                'phone' => '+212539000003',
                'email' => 'contact@iberiatravel.com',
                'user_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('companies')->insert($companies);
    }
}

