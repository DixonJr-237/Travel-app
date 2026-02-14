<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            // Notifications for Super Admin (user_id 1)
            [
                'message' => 'New company "EuroBus International" has been registered',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'company_created',
                'data' => json_encode(['company_id' => 1, 'company_name' => 'EuroBus International']),
                'user_id' => 1,
            ],
            [
                'message' => 'New company "Marrakech Express" has been registered',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'company_created',
                'data' => json_encode(['company_id' => 2, 'company_name' => 'Marrakech Express']),
                'user_id' => 1,
            ],

            // Notifications for Company Admins
            [
                'message' => 'Your company profile has been approved',
                'status' => 'read',
                'created_at' => now()->subDays(1),
                'type' => 'company_approved',
                'data' => json_encode(['company_id' => 1]),
                'user_id' => 2, // EuroBus Admin
            ],
            [
                'message' => 'New agency "EuroBus Casablanca Central" has been created',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'agency_created',
                'data' => json_encode(['agency_id' => 1, 'agency_name' => 'EuroBus Casablanca Central']),
                'user_id' => 2, // EuroBus Admin
            ],
            [
                'message' => 'Your company profile has been approved',
                'status' => 'read',
                'created_at' => now()->subDays(1),
                'type' => 'company_approved',
                'data' => json_encode(['company_id' => 2]),
                'user_id' => 3, // Marrakech Express Admin
            ],

            // Notifications for Agency Admins
            [
                'message' => 'Your agency account has been created successfully',
                'status' => 'read',
                'created_at' => now()->subDays(2),
                'type' => 'agency_created',
                'data' => json_encode(['agency_id' => 1]),
                'user_id' => 5, // EuroBus Casablanca
            ],
            [
                'message' => 'New trip scheduled: Casablanca to Rabat',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'trip_scheduled',
                'data' => json_encode(['trip_id' => 1, 'journey' => 'Casablanca to Rabat']),
                'user_id' => 5, // EuroBus Casablanca
            ],
            [
                'message' => 'New booking received for Casablanca to Rabat trip',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'booking_received',
                'data' => json_encode(['ticket_id' => 1, 'trip_id' => 1]),
                'user_id' => 5, // EuroBus Casablanca
            ],
            [
                'message' => 'Your agency account has been created successfully',
                'status' => 'read',
                'created_at' => now()->subDays(2),
                'type' => 'agency_created',
                'data' => json_encode(['agency_id' => 3]),
                'user_id' => 7, // Marrakech Express Marrakech
            ],
            [
                'message' => 'New trip scheduled: Marrakech to Agadir',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'trip_scheduled',
                'data' => json_encode(['trip_id' => 9, 'journey' => 'Marrakech to Agadir']),
                'user_id' => 7, // Marrakech Express Marrakech
            ],

            // Notifications for Customers
            [
                'message' => 'Your ticket booking has been confirmed! Booking Reference: EB...',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'ticket_confirmed',
                'data' => json_encode(['ticket_id' => 1]),
                'user_id' => 11, // Mohammed Benali
            ],
            [
                'message' => 'Your ticket booking has been confirmed! Booking Reference: EB...',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'ticket_confirmed',
                'data' => json_encode(['ticket_id' => 2]),
                'user_id' => 12, // Fatima Zahra
            ],
            [
                'message' => 'Your ticket booking has been confirmed! Booking Reference: EB...',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'ticket_confirmed',
                'data' => json_encode(['ticket_id' => 3]),
                'user_id' => 13, // Ahmed Kadiri
            ],
            [
                'message' => 'Your ticket booking is pending confirmation',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'ticket_pending',
                'data' => json_encode(['ticket_id' => 7]),
                'user_id' => 14, // Aicha Mourad
            ],
            [
                'message' => 'Your ticket has been cancelled',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'ticket_cancelled',
                'data' => json_encode(['ticket_id' => 12]),
                'user_id' => 22, // Souad Mekki
            ],
            [
                'message' => 'Reminder: Your trip from Tanger to Fès is tomorrow',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'trip_reminder',
                'data' => json_encode(['ticket_id' => 8, 'trip_id' => 12]),
                'user_id' => 18, // Nadia Chakir
            ],

            // More system notifications
            [
                'message' => 'Welcome to Travel App! Start booking your trips now.',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'welcome',
                'data' => null,
                'user_id' => 11, // Mohammed Benali
            ],
            [
                'message' => 'Welcome to Travel App! Start booking your trips now.',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'welcome',
                'data' => null,
                'user_id' => 12, // Fatima Zahra
            ],
            [
                'message' => 'Bus EB-MA-1003 has been scheduled for maintenance',
                'status' => 'unread',
                'created_at' => now(),
                'type' => 'maintenance',
                'data' => json_encode(['bus_id' => 3, 'bus_registration' => 'EB-MA-1003']),
                'user_id' => 5, // EuroBus Casablanca Agency Admin
            ],
        ];

        DB::table('notifications')->insert($notifications);
    }
}

