<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Call seeders in correct dependency order

        // 1. Users first (no dependencies)
        $this->call([
            UserSeeder::class,
        ]);

        // 2. Companies (depends on users)
        $this->call([
            CompanySeeder::class,
        ]);

        // 3. Geographic data (countries, regions, sub_regions, cities)
        $this->call([
            CountrySeeder::class,
            RegionSeeder::class,
            SubRegionSeeder::class,
            CitySeeder::class,
        ]);

        // 4. Coordinates (depends on cities)
        $this->call([
            CoordinateSeeder::class,
        ]);

        // 5. Agencies (depends on users, companies, coordinates, cities)
        $this->call([
            AgencySeeder::class,
        ]);

        // 6. Agency Activities (depends on companies, agencies, regions, coordinates)
        $this->call([
            AgencyActivitySeeder::class,
        ]);

        // 7. Buses (depends on agencies)
        $this->call([
            BusSeeder::class,
        ]);

        // 8. Journeys (depends on coordinates)
        $this->call([
            JourneySeeder::class,
        ]);

        // 9. Trips (depends on buses, journeys, coordinates)
        $this->call([
            TripSeeder::class,
        ]);

        // 10. Customers (depends on users)
        $this->call([
            CustomerSeeder::class,
        ]);

        // 11. Tickets (depends on journeys, customers, trips)
        $this->call([
            TicketSeeder::class,
        ]);

        // 12. Reservations (depends on tickets)
        $this->call([
            ReservationSeeder::class,
        ]);

        // 13. Notifications (depends on users)
        $this->call([
            NotificationSeeder::class,
        ]);
    }
}

