<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Morocco', 'code' => 'MA', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Spain', 'code' => 'ES', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'France', 'code' => 'FR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Germany', 'code' => 'DE', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Portugal', 'code' => 'PT', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('countries')->insert($countries);
    }
}

