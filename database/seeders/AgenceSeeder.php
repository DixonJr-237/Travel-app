<?php

namespace Database\Seeders;

use App\Models\Agence;
use App\Models\City;
use App\Models\Company;
use App\Models\Coordinate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgenceSeeder extends Seeder
{


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if required tables exist
        try {
            $companiesCount = Company::count();
            $citiesCount = City::count();
            $coordsCount = Coordinate::count();
        } catch (\Exception $e) {
            $this->command->error('Required tables (companies, cities, coordinates) do not exist. Please run migrations first.');
            $this->command->info('Run: php artisan migrate');
            return;
        }

        if ($companiesCount === 0) {
            $this->command->info('No companies found. Please seed companies first.');
            return;
        }

        if ($citiesCount === 0) {
            $this->command->info('No cities found. Please seed cities first.');
            return;
        }

        if ($coordsCount === 0) {
            $this->command->info('No coordinates found. Please seed coordinates first.');
            return;
        }

        $companies = Company::all();
        $createdCount = 0;

        foreach ($companies as $company) {
            // Create 2-3 agencies per company
            $agencyCount = rand(2, 3);
            $cities = City::limit(10)->get();

            for ($i = 1; $i <= $agencyCount; $i++) {
                $city = $cities->random();
                $coord = Coordinate::where('id_city', $city->id_city)->first();

                if (!$coord) {
                    continue;
                }

                // Check if agency already exists
                $agencyName = $company->name . ' - ' . $city->name . ' Branch';
                $existingAgency = Agence::where('name', $agencyName)->first();

                if ($existingAgency) {
                    $this->command->info("Agency '{$agencyName}' already exists, skipping...");
                    continue;
                }

                // Create agency admin user (or get existing)
                $adminEmail = strtolower(str_replace(' ', '', $company->name)) . '_agency' . $i . '@example.com';
                $adminUser = User::firstOrCreate(
                    ['email' => $adminEmail],
                    [
                        'name' => $company->name . ' Agency ' . $i . ' Manager',
                        'password' => Hash::make('password123'),
                        'role' => 'agency_admin',
                        'phone' => '+33 1 23 45 67 ' . str_pad($i * 10, 2, '0', STR_PAD_LEFT),
                    ]
                );

                // Create agency
                $agency = Agence::create([
                    'name' => $agencyName,
                    'email' => strtolower(str_replace(' ', '', $company->name)) . '_' . strtolower($city->name) . '@example.com',
                    'phone' => '+33 1 23 45 67 ' . str_pad($i * 11, 2, '0', STR_PAD_LEFT),
                    'id_company' => $company->id_company,
                    'id_coord' => $coord->id_coord,
                    'id_city' => $city->id_city,
                    'user_id' => $adminUser->id,
                ]);

                // Note: We don't update user with agency_id as per requirement
                // The relationship is handled through the agency table

                $createdCount++;
            }
        }

        $this->command->info("Agencies seeded successfully! ({$createdCount} created)");
    }
}

