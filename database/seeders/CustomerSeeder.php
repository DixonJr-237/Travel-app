<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Customer user IDs from UserSeeder are 11-30
        $customers = [
            [
                'first_name' => 'Mohammed',
                'last_name' => 'Benali',
                'phone' => '+212600100001',
                'email' => 'mohammed.benali@gmail.com',
                'user_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Zahra',
                'phone' => '+212600100002',
                'email' => 'fatima.zahra@gmail.com',
                'user_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Kadiri',
                'phone' => '+212600100003',
                'email' => 'ahmed.kadiri@gmail.com',
                'user_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Aicha',
                'last_name' => 'Mourad',
                'phone' => '+212600100004',
                'email' => 'aicha.mourad@gmail.com',
                'user_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Youssef',
                'last_name' => 'Amrani',
                'phone' => '+212600100005',
                'email' => 'youssef.amrani@gmail.com',
                'user_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Samira',
                'last_name' => 'El Amrani',
                'phone' => '+212600100006',
                'email' => 'samira.elamrani@gmail.com',
                'user_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Rachid',
                'last_name' => 'Belhaj',
                'phone' => '+212600100007',
                'email' => 'rachid.belhaj@gmail.com',
                'user_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Nadia',
                'last_name' => 'Chakir',
                'phone' => '+212600100008',
                'email' => 'nadia.chakir@gmail.com',
                'user_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Idrissi',
                'phone' => '+212600100009',
                'email' => 'omar.idrissi@gmail.com',
                'user_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Laila',
                'last_name' => 'Bennis',
                'phone' => '+212600100010',
                'email' => 'laila.bennis@gmail.com',
                'user_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Hicham',
                'last_name' => 'Faouzi',
                'phone' => '+212600100011',
                'email' => 'hicham.faouzi@gmail.com',
                'user_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Souad',
                'last_name' => 'Mekki',
                'phone' => '+212600100012',
                'email' => 'souad.mekki@gmail.com',
                'user_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Karim',
                'last_name' => 'Bouazza',
                'phone' => '+212600100013',
                'email' => 'karim.bouazza@gmail.com',
                'user_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Amina',
                'last_name' => 'Tazi',
                'phone' => '+212600100014',
                'email' => 'amina.tazi@gmail.com',
                'user_id' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Abdelkrim',
                'last_name' => 'Hajji',
                'phone' => '+212600100015',
                'email' => 'abdelkrim.hajji@gmail.com',
                'user_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Halima',
                'last_name' => 'Essafi',
                'phone' => '+212600100016',
                'email' => 'halima.essafi@gmail.com',
                'user_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Tarik',
                'last_name' => 'Mouhcine',
                'phone' => '+212600100017',
                'email' => 'tarik.mouhcine@gmail.com',
                'user_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Asmaa',
                'last_name' => 'Rhazi',
                'phone' => '+212600100018',
                'email' => 'asmaa.rhazi@gmail.com',
                'user_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Anas',
                'last_name' => 'Sqalli',
                'phone' => '+212600100019',
                'email' => 'anas.sqalli@gmail.com',
                'user_id' => 29,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Rania',
                'last_name' => 'Lahlou',
                'phone' => '+212600100020',
                'email' => 'rania.lahlou@gmail.com',
                'user_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('customers')->insert($customers);
    }
}

