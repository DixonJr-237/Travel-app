# Travel App - TODO

## Recent Fixes

### Login Fix (Completed: 2024)
- [x] Fixed login not working - User model was missing `$primaryKey = 'user_id'`
- [x] The database migration uses `user_id` as primary key but Laravel default is `id`
- [x] Added `protected $primaryKey = 'user_id'` to User model
- [x] Tested all user roles: super_admin, company_admin, agency_admin, customer
- [x] Verified role-based access control works correctly

### Test Credentials:
- Super Admin: `superadmin@travelapp.com` / `superadmin123`
- Company Admin: `admin@eurobus.com` / `password123`
- Agency Admin: `eurobus_casablanca@example.com` / `password123`
- Customer: `mohammed.benali@gmail.com` / `password123`

---

## Previous Seeder TODO (Completed)

### Seeder Files Created (in dependency order):

- [x] 1. UserSeeder - Create users (super_admin, company_admin, agency_admin, customer)
- [x] 2. CompanySeeder - Create companies with company_admin users
- [x] 3. CountrySeeder - Create countries
- [x] 4. RegionSeeder - Create regions
- [x] 5. SubRegionSeeder - Create sub_regions
- [x] 6. CitySeeder - Create cities
- [x] 7. CoordinateSeeder - Create coordinates with addresses
- [x] 8. AgencySeeder - Create agencies
- [x] 9. AgencyActivitySeeder - Create agency activities
- [x] 10. BusSeeder - Create buses
- [x] 11. JourneySeeder - Create journeys
- [x] 12. TripSeeder - Create trips (tips table)
- [x] 13. CustomerSeeder - Create customers
- [x] 14. TicketSeeder - Create tickets
- [x] 15. ReservationSeeder - Create reservations
- [x] 16. NotificationSeeder - Create notifications
- [x] 17. Update DatabaseSeeder - Call all seeders in correct order
- [x] 18. Test seeder - Run php artisan db:seed

### Notes:
- Must respect foreign key dependencies
- Use proper Laravel seeder conventions
- Include realistic sample data for testing
- ✅ All seeders created and tested successfully

