<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoordinateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinates = [
            // Morocco - Casablanca area (city_id 1-3)
            ['geo_coord' => '33.5731,-7.5898', 'latitude' => 33.5731, 'longitude' => -7.5898, 'address' => 'Casa Voyageurs Station, Casablanca', 'id_city' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '33.5883,-7.3624', 'latitude' => 33.5883, 'longitude' => -7.3624, 'address' => 'Central Station, Mohammedia', 'id_city' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '33.2551,-8.5062', 'latitude' => 33.2551, 'longitude' => -8.5062, 'address' => 'El Jadida Bus Station', 'id_city' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Rabat-Salé-Kénitra (city_id 4-7)
            ['geo_coord' => '34.0209,-6.8416', 'latitude' => 34.0209, 'longitude' => -6.8416, 'address' => 'Rabat Agdal Station', 'id_city' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '34.0381,-6.8016', 'latitude' => 34.0381, 'longitude' => -6.8016, 'address' => 'Salé Terminal', 'id_city' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '34.0625,-6.7955', 'latitude' => 34.0625, 'longitude' => -6.7955, 'address' => 'Témara Bus Station', 'id_city' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '34.2551,-6.5898', 'latitude' => 34.2551, 'longitude' => -6.5898, 'address' => 'Kénitra Station', 'id_city' => 7, 'created_at' => now(), 'updated_at' => now()],

            // Marrakech-Safi (city_id 8-10)
            ['geo_coord' => '31.6295,-7.9811', 'latitude' => 31.6295, 'longitude' => -7.9811, 'address' => 'Marrakech Bus Station', 'id_city' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '31.5085,-9.7595', 'latitude' => 31.5085, 'longitude' => -9.7595, 'address' => 'Essaouira Port', 'id_city' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '32.2936,-9.2371', 'latitude' => 32.2936, 'longitude' => -9.2371, 'address' => 'Safi Train Station', 'id_city' => 10, 'created_at' => now(), 'updated_at' => now()],

            // Souss-Massa (city_id 11-13)
            ['geo_coord' => '30.4278,-9.5982', 'latitude' => 30.4278, 'longitude' => -9.5982, 'address' => 'Agadir Bus Station', 'id_city' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '30.3678,-9.5401', 'latitude' => 30.3678, 'longitude' => -9.5401, 'address' => 'Inezgane Terminal', 'id_city' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '29.6962,-9.7316', 'latitude' => 29.6962, 'longitude' => -9.7316, 'address' => 'Tiznit Station', 'id_city' => 13, 'created_at' => now(), 'updated_at' => now()],

            // Tanger-Tétouan (city_id 14-16)
            ['geo_coord' => '35.7595,-5.8340', 'latitude' => 35.7595, 'longitude' => -5.8340, 'address' => 'Tanger Ville Station', 'id_city' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '35.5892,-5.3626', 'latitude' => 35.5892, 'longitude' => -5.3626, 'address' => 'Tétouan Bus Station', 'id_city' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '35.7270,-5.2964', 'latitude' => 35.7270, 'longitude' => -5.2964, 'address' => 'Fnideq Border', 'id_city' => 16, 'created_at' => now(), 'updated_at' => now()],

            // Fès-Meknès (city_id 17-19)
            ['geo_coord' => '34.0331,-5.0003', 'latitude' => 34.0331, 'longitude' => -5.0003, 'address' => 'Fès Train Station', 'id_city' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '33.8935,-5.5470', 'latitude' => 33.8935, 'longitude' => -5.5470, 'address' => 'Meknès Bus Station', 'id_city' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '33.5286,-5.1065', 'latitude' => 33.5286, 'longitude' => -5.1065, 'address' => 'Ifrane Station', 'id_city' => 19, 'created_at' => now(), 'updated_at' => now()],

            // Spain (city_id 20-23)
            ['geo_coord' => '37.3891,-5.9845', 'latitude' => 37.3891, 'longitude' => -5.9845, 'address' => 'Seville Plaza de Armas', 'id_city' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '36.7213,-4.4214', 'latitude' => 36.7213, 'longitude' => -4.4214, 'address' => 'Malaga Bus Station', 'id_city' => 21, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '40.4168,-3.7038', 'latitude' => 40.4168, 'longitude' => -3.7038, 'address' => 'Madrid Atocha', 'id_city' => 22, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '41.3851,2.1734', 'latitude' => 41.3851, 'longitude' => 2.1734, 'address' => 'Barcelona Nord', 'id_city' => 23, 'created_at' => now(), 'updated_at' => now()],

            // France (city_id 24-25)
            ['geo_coord' => '48.8566,2.3522', 'latitude' => 48.8566, 'longitude' => 2.3522, 'address' => 'Paris Gare de Lyon', 'id_city' => 24, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '43.2965,5.3698', 'latitude' => 43.2965, 'longitude' => 5.3698, 'address' => 'Marseille Saint-Charles', 'id_city' => 25, 'created_at' => now(), 'updated_at' => now()],

            // Germany (city_id 26-27)
            ['geo_coord' => '52.5200,13.4050', 'latitude' => 52.5200, 'longitude' => 13.4050, 'address' => 'Berlin ZOB', 'id_city' => 26, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '48.1351,11.5820', 'latitude' => 48.1351, 'longitude' => 11.5820, 'address' => 'Munich ZOB', 'id_city' => 27, 'created_at' => now(), 'updated_at' => now()],

            // Portugal (city_id 28-29)
            ['geo_coord' => '38.7223,-9.1393', 'latitude' => 38.7223, 'longitude' => -9.1393, 'address' => 'Lisbon Sete Rios', 'id_city' => 28, 'created_at' => now(), 'updated_at' => now()],
            ['geo_coord' => '41.1579,-8.6291', 'latitude' => 41.1579, 'longitude' => -8.6291, 'address' => 'Porto Campo 24 Agosto', 'id_city' => 29, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('coordinates')->insert($coordinates);
    }
}

