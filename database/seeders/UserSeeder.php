<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@travelapp.com',
            'phone' => '+212600000001',
            'role' => 'super_admin',
            'password' => Hash::make('superadmin123'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Company Admins
        $companyAdmins = [
            [
                'name' => 'EuroBus Admin',
                'email' => 'admin@eurobus.com',
                'phone' => '+212600000002',
                'role' => 'company_admin',
            ],
            [
                'name' => 'Marrakech Express Admin',
                'email' => 'admin@marrakechexpress.com',
                'phone' => '+212600000003',
                'role' => 'company_admin',
            ],
            [
                'name' => 'Iberia Travel Admin',
                'email' => 'admin@iberiatravel.com',
                'phone' => '+212600000004',
                'role' => 'company_admin',
            ],
        ];

        foreach ($companyAdmins as $admin) {
            DB::table('users')->insert([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'phone' => $admin['phone'],
                'role' => $admin['role'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Agency Admins (6 agencies - 2 per company)
        $agencyAdmins = [
            // EuroBus agencies
            ['name' => 'EuroBus Casablanca Agency', 'email' => 'eurobus_casablanca@example.com', 'phone' => '+212600000010'],
            ['name' => 'EuroBus Rabat Agency', 'email' => 'eurobus_rabat@example.com', 'phone' => '+212600000011'],
            // Marrakech Express agencies
            ['name' => 'Marrakech Express Marrakech Agency', 'email' => 'marrakechexpress_marrakech@example.com', 'phone' => '+212600000012'],
            ['name' => 'Marrakech Express Agadir Agency', 'email' => 'marrakechexpress_agadir@example.com', 'phone' => '+212600000013'],
            // Iberia Travel agencies
            ['name' => 'Iberia Travel Tangier Agency', 'email' => 'iberiatravel_tangier@example.com', 'phone' => '+212600000014'],
            ['name' => 'Iberia Travel Fez Agency', 'email' => 'iberiatravel_fez@example.com', 'phone' => '+212600000015'],
        ];

        foreach ($agencyAdmins as $admin) {
            DB::table('users')->insert([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'phone' => $admin['phone'],
                'role' => 'agency_admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Customers (20 sample customers)
        $customers = [
            ['name' => 'Mohammed Benali', 'email' => 'mohammed.benali@gmail.com', 'phone' => '+212600100001'],
            ['name' => 'Fatima Zahra', 'email' => 'fatima.zahra@gmail.com', 'phone' => '+212600100002'],
            ['name' => 'Ahmed Kadiri', 'email' => 'ahmed.kadiri@gmail.com', 'phone' => '+212600100003'],
            ['name' => 'Aicha Mourad', 'email' => 'aicha.mourad@gmail.com', 'phone' => '+212600100004'],
            ['name' => 'Youssef Amrani', 'email' => 'youssef.amrani@gmail.com', 'phone' => '+212600100005'],
            ['name' => 'Samira El Amrani', 'email' => 'samira.elamrani@gmail.com', 'phone' => '+212600100006'],
            ['name' => 'Rachid Belhaj', 'email' => 'rachid.belhaj@gmail.com', 'phone' => '+212600100007'],
            ['name' => 'Nadia Chakir', 'email' => 'nadia.chakir@gmail.com', 'phone' => '+212600100008'],
            ['name' => 'Omar Idrissi', 'email' => 'omar.idrissi@gmail.com', 'phone' => '+212600100009'],
            ['name' => 'Laila Bennis', 'email' => 'laila.bennis@gmail.com', 'phone' => '+212600100010'],
            ['name' => 'Hicham Faouzi', 'email' => 'hicham.faouzi@gmail.com', 'phone' => '+212600100011'],
            ['name' => 'Souad Mekki', 'email' => 'souad.mekki@gmail.com', 'phone' => '+212600100012'],
            ['name' => 'Karim Bouazza', 'email' => 'karim.bouazza@gmail.com', 'phone' => '+212600100013'],
            ['name' => 'Amina Tazi', 'email' => 'amina.tazi@gmail.com', 'phone' => '+212600100014'],
            ['name' => 'Abdelkrim Hajji', 'email' => 'abdelkrim.hajji@gmail.com', 'phone' => '+212600100015'],
            ['name' => 'Halima Essafi', 'email' => 'halima.essafi@gmail.com', 'phone' => '+212600100016'],
            ['name' => 'Tarik Mouhcine', 'email' => 'tarik.mouhcine@gmail.com', 'phone' => '+212600100017'],
            ['name' => 'Asmaa Rhazi', 'email' => 'asmaa.rhazi@gmail.com', 'phone' => '+212600100018'],
            ['name' => 'Anas Sqalli', 'email' => 'anas.sqalli@gmail.com', 'phone' => '+212600100019'],
            ['name' => 'Rania Lahlou', 'email' => 'rania.lahlou@gmail.com', 'phone' => '+212600100020'],
        ];

        foreach ($customers as $customer) {
            DB::table('users')->insert([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'role' => 'customer',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

