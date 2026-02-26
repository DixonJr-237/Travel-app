<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TipsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes - BusSwift Travel Management System
|--------------------------------------------------------------------------
*/

// =========================================================================
// CONFIGURATION & HELPERS
// =========================================================================
// Define role constants for consistency
if (!defined('ROLE_SUPER_ADMIN')) define('ROLE_SUPER_ADMIN', 'super_admin');
if (!defined('ROLE_COMPANY_ADMIN')) define('ROLE_COMPANY_ADMIN', 'company_admin');
if (!defined('ROLE_AGENCY_ADMIN')) define('ROLE_AGENCY_ADMIN', 'agency_admin');
if (!defined('ROLE_CUSTOMER')) define('ROLE_CUSTOMER', 'customer');

// =========================================================================
// PUBLIC ROUTES (No Authentication Required)
// =========================================================================
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Public trip search and info
Route::name('public.')->group(function () {
    Route::get('/search-trips', [TipsController::class, 'publicSearch'])->name('trips.search');
    Route::get('/trip-schedule', [TipsController::class, 'publicSchedule'])->name('trips.schedule');

    // Public ticket verification
    Route::get('/tickets/verify/{reference}', [TicketController::class, 'verify'])->name('tickets.verify');
    Route::get('/tickets/qr/{reference}', [TicketController::class, 'qrCode'])->name('tickets.qr');

    // Company/agency public info
    Route::get('/companies/{company}/public', [CompanyController::class, 'publicShow'])->name('companies.public.show');
    Route::get('/agencies/{agency}/public', [AgencyController::class, 'publicShow'])->name('agencies.public.show');
});

// Static pages
Route::view('/api-docs', 'api-docs')->name('api.docs');
Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/contact', 'contact')->name('contact');

// =========================================================================
// AUTHENTICATION ROUTES (Loaded from auth.php)
// =========================================================================
require __DIR__.'/auth.php';

// =========================================================================
// PROTECTED ROUTES (Require Authentication)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // Email verification requirement - apply only to routes that need it
    Route::middleware(['verified'])->group(function () {

        // =================================================================
        // DASHBOARD & HOME
        // =================================================================
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/search', [DashboardController::class, 'search'])->name('dashboard.search');
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // =================================================================
        // PROFILE MANAGEMENT
        // =================================================================
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });

        // =================================================================
        // SUPER ADMIN ROUTES
        // =================================================================
        Route::middleware(['role:' . ROLE_SUPER_ADMIN])
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {

            // ============ COMPANIES MANAGEMENT ============
            Route::resource('companies', CompanyController::class)
                ->names([
                    'index' => 'companies.index',
                    'create' => 'companies.create',
                    'store' => 'companies.store',
                    'show' => 'companies.show',
                    'edit' => 'companies.edit',
                    'update' => 'companies.update',
                    'destroy' => 'companies.destroy',
                ]);

            Route::controller(CompanyController::class)
                ->prefix('companies')
                ->name('companies.')
                ->group(function () {
                    Route::post('{company}/toggle-status', 'toggleStatus')->name('toggle-status');
                    Route::get('{company}/activities', 'activities')->name('activities');
                    Route::get('export', 'export')->name('export');
                    Route::get('search', 'search')->name('search');
                });

            // ============ AGENCIES MANAGEMENT ============
            Route::resource('agencies', AgencyController::class)
                ->names([
                    'index' => 'agencies.index',
                    'create' => 'agencies.create',
                    'store' => 'agencies.store',
                    'show' => 'agencies.show',
                    'edit' => 'agencies.edit',
                    'update' => 'agencies.update',
                    'destroy' => 'agencies.destroy',
                ]);

            Route::controller(AgencyController::class)
                ->prefix('agencies')
                ->name('agencies.')
                ->group(function () {
                    Route::post('{agency}/activate', 'activate')->name('activate');
                    Route::post('{agency}/deactivate', 'deactivate')->name('deactivate');
                    Route::post('{agency}/suspend', 'suspend')->name('suspend');
                    Route::post('bulk-action', 'bulkAction')->name('bulk-action');
                    Route::get('export', 'export')->name('export');
                    Route::get('search', 'search')->name('search');
                    Route::get('{agency}/activities', 'activities')->name('activities');
                });

            // ============ BUSES MANAGEMENT ============
            Route::resource('buses', BusController::class)
                ->names([
                    'index' => 'buses.index',
                    'create' => 'buses.create',
                    'store' => 'buses.store',
                    'show' => 'buses.show',
                    'edit' => 'buses.edit',
                    'update' => 'buses.update',
                    'destroy' => 'buses.destroy',
                ]);

            Route::controller(BusController::class)
                ->prefix('buses')
                ->name('buses.')
                ->group(function () {
                    Route::post('{bus}/status', 'updateStatus')->name('status.update');
                    Route::post('{bus}/maintenance', 'maintenance')->name('maintenance');
                });

            // ============ TRIPS MANAGEMENT ============
            Route::resource('trips', TripController::class)
                ->names([
                    'index' => 'trips.index',
                    'create' => 'trips.create',
                    'store' => 'trips.store',
                    'show' => 'trips.show',
                    'edit' => 'trips.edit',
                    'update' => 'trips.update',
                    'destroy' => 'trips.destroy',
                ]);

            Route::controller(TripController::class)
                ->prefix('trips')
                ->name('trips.')
                ->group(function () {
                    Route::post('{trip}/status', 'updateStatus')->name('status.update');
                    Route::get('{trip}/seats', 'seats')->name('seats');
                    Route::post('{trip}/seats/update', 'updateSeats')->name('seats.update');
                    Route::get('{trip}/passengers', 'passengers')->name('passengers');
                    Route::get('schedule/create', 'createSchedule')->name('schedule.create');
                    Route::post('schedule/store', 'storeSchedule')->name('schedule.store');
                });
        });

        // =================================================================
        // COMPANY ADMIN ROUTES
        // =================================================================
        Route::middleware(['role:' . ROLE_COMPANY_ADMIN])
            ->prefix('my-company')
            ->name('my-company.')
            ->group(function () {

                // Company management
                Route::controller(CompanyController::class)->group(function () {
                    Route::get('/', 'myCompany')->name('dashboard');
                    Route::get('/company/{company}', [CompanyController::class, 'show'])->name('companies.show');
                    Route::get('/company/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
                    Route::put('/company/{company}', [CompanyController::class, 'update'])->name('companies.update');
                    Route::get('/edit', 'editMyCompany')->name('edit');
                    Route::patch('/update', 'updateMyCompany')->name('update');
                    Route::get('/agencies', 'myAgencies')->name('agencies'); // This is the agencies route
                    Route::get('/buses', 'myBuses')->name('buses');
                    Route::get('/trips', 'myTrips')->name('trips');
                    Route::get('/reports', 'myReports')->name('reports');
                    Route::get('/users', 'myUsers')->name('users');
                });

                 // ============ AGENCIES MANAGEMENT ============
            Route::resource('agencies', AgencyController::class)
                ->names([
                    'index' => 'agencies.index',
                    'create' => 'agencies.create',
                    'store' => 'agencies.store',
                    'show' => 'agencies.show',
                    'edit' => 'agencies.edit',
                    'update' => 'agencies.update',
                    'destroy' => 'agencies.destroy',
                ]);

            Route::controller(AgencyController::class)
                ->prefix('agencies')
                ->name('agencies.')
                ->group(function () {
                    Route::post('{agency}/activate', 'activate')->name('activate');
                    Route::post('{agency}/deactivate', 'deactivate')->name('deactivate');
                    Route::post('{agency}/suspend', 'suspend')->name('suspend');
                    Route::post('bulk-action', 'bulkAction')->name('bulk-action');
                    Route::get('export', 'export')->name('export');
                    Route::get('search', 'search')->name('search');
                    Route::get('{agency}/activities', 'activities')->name('activities');
                });

            // ============ BUSES MANAGEMENT ============
            Route::resource('buses', BusController::class)
                ->names([
                    'index' => 'buses.index',
                    'create' => 'buses.create',
                    'store' => 'buses.store',
                    'show' => 'buses.show',
                    'edit' => 'buses.edit',
                    'update' => 'buses.update',
                    'destroy' => 'buses.destroy',
                ]);

            Route::controller(BusController::class)
                ->prefix('buses')
                ->name('buses.')
                ->group(function () {
                    Route::post('{bus}/status', 'updateStatus')->name('status.update');
                    Route::post('{bus}/maintenance', 'maintenance')->name('maintenance');
                });

            // ============ TRIPS MANAGEMENT ============
            Route::resource('trips', TripController::class)
                ->names([
                    'index' => 'trips.index',
                    'create' => 'trips.create',
                    'store' => 'trips.store',
                    'show' => 'trips.show',
                    'edit' => 'trips.edit',
                    'update' => 'trips.update',
                    'destroy' => 'trips.destroy',
                ]);

            Route::controller(TripController::class)
                ->prefix('trips')
                ->name('trips.')
                ->group(function () {
                    Route::post('{trip}/status', 'updateStatus')->name('status.update');
                    Route::get('{trip}/seats', 'seats')->name('seats');
                    Route::post('{trip}/seats/update', 'updateSeats')->name('seats.update');
                    Route::get('{trip}/passengers', 'passengers')->name('passengers');
                    Route::get('schedule/create', 'createSchedule')->name('schedule.create');
                    Route::post('schedule/store', 'storeSchedule')->name('schedule.store');
                });

                // Add explicit index routes for resource-like access
                Route::get('/agencies/index', [CompanyController::class, 'myAgencies'])->name('agencies.index');
                Route::get('/buses/index', [CompanyController::class, 'myBuses'])->name('buses.index');
                Route::get('/trips/index', [CompanyController::class, 'myTrips'])->name('trips.index');
                Route::get('/reports/index', [CompanyController::class, 'myReports'])->name('reports.index');
            });

        // =================================================================
        // AGENCY ADMIN ROUTES
        // =================================================================
        Route::middleware(['role:' . ROLE_AGENCY_ADMIN])
            ->prefix('my-agency')
            ->name('my-agency.')
            ->group(function () {

            // Agency management
            Route::controller(AgencyController::class)->group(function () {
                Route::get('/', 'myAgency')->name('dashboard');
                Route::get('/agency/{agency}', [AgencyController::class, 'show'])->name('agencies.show');
                Route::get('/agency/{agency}/edit', [AgencyController::class, 'edit'])->name('agencies.edit');
                Route::get('/edit', 'editMyAgency')->name('edit');
                Route::patch('/update', 'updateMyAgency')->name('update');
                Route::get('/reports', 'myReports')->name('reports');
                Route::get('/activities', 'myActivities')->name('activities');
            });

            // Buses Management - Full resource
            Route::resource('buses', BusController::class)
                ->names([
                    'index' => 'buses.index',
                    'create' => 'buses.create',
                    'store' => 'buses.store',
                    'show' => 'buses.show',
                    'edit' => 'buses.edit',
                    'update' => 'buses.update',
                    'destroy' => 'buses.destroy',
                ]);

            // Bus Operations
            Route::controller(BusController::class)
                ->prefix('buses')
                ->name('buses.')
                ->group(function () {
                    Route::post('{bus}/status', 'updateStatus')->name('status.update');
                    Route::post('{bus}/maintenance', 'maintenance')->name('maintenance');
                });

            // Trips Management - Full resource
            Route::resource('trips', TripController::class)
                ->names([
                    'index' => 'trips.index',
                    'create' => 'trips.create',
                    'store' => 'trips.store',
                    'show' => 'trips.show',
                    'edit' => 'trips.edit',
                    'update' => 'trips.update',
                    'destroy' => 'trips.destroy',
                ]);

            // Trip Operations
            Route::controller(TripController::class)
                ->prefix('trips')
                ->name('trips.')
                ->group(function () {
                    Route::post('{trip}/status', 'updateStatus')->name('status.update');
                    Route::get('{trip}/seats', 'seats')->name('seats');
                    Route::post('{trip}/seats/update', 'updateSeats')->name('seats.update');
                    Route::get('{trip}/passengers', 'passengers')->name('passengers');
                });
        });

        // =================================================================
        // TICKET ROUTES (Multi-role access)
        // =================================================================
        Route::controller(TicketController::class)->group(function () {

            // Common ticket operations (accessible by multiple roles)
            Route::middleware(['role:' . implode('|', [ROLE_CUSTOMER, ROLE_AGENCY_ADMIN, ROLE_COMPANY_ADMIN, ROLE_SUPER_ADMIN])])
                ->group(function () {
                    Route::get('tickets', 'index')->name('tickets.index');
                    Route::get('tickets/{ticket}', 'show')->name('tickets.show');
                    Route::get('tickets/{ticket}/edit', 'edit')->name('tickets.edit');
                    Route::put('tickets/{ticket}', 'update')->name('tickets.update');
                    Route::delete('tickets/{ticket}', 'destroy')->name('tickets.destroy');

                    Route::post('tickets/{ticket}/cancel', 'cancel')->name('tickets.cancel');
                    Route::get('tickets/{ticket}/print', 'print')->name('tickets.print');
                    Route::get('tickets/{ticket}/download', 'download')->name('tickets.download');
                });

            // Customer-specific routes
            Route::middleware(['role:' . ROLE_CUSTOMER])->group(function () {
                Route::get('tickets/create/{trip}', 'create')->name('tickets.create');
                Route::post('tickets/store/{trip}', 'store')->name('tickets.store');
                Route::get('my-tickets', 'myTickets')->name('tickets.my');
            });

            // Admin ticket sales (agency, company, super admin)
            Route::middleware(['role:' . implode('|', [ROLE_AGENCY_ADMIN, ROLE_COMPANY_ADMIN, ROLE_SUPER_ADMIN])])
                ->group(function () {
                    Route::get('tickets/sell', 'sell')->name('tickets.sell');
                });

            // Booking flow (customers)
            Route::middleware(['role:' . ROLE_CUSTOMER])->group(function () {
                Route::get('booking/search', 'searchTrips')->name('tickets.booking.search');
                Route::get('booking/{trip}/select-seats', 'selectSeats')->name('tickets.booking.select-seats');
                Route::post('booking/{trip}/confirm', 'confirmBooking')->name('tickets.booking.confirm');
                Route::post('booking/{trip}/process-payment', 'processPayment')->name('tickets.booking.process-payment');
            });
        });

        // =================================================================
        // CUSTOMER MANAGEMENT (Admin roles)
        // =================================================================
        Route::middleware(['role:' . implode('|', [ROLE_SUPER_ADMIN, ROLE_COMPANY_ADMIN, ROLE_AGENCY_ADMIN])])
            ->resource('customers', CustomerController::class);

        Route::middleware(['role:' . implode('|', [ROLE_SUPER_ADMIN, ROLE_COMPANY_ADMIN, ROLE_AGENCY_ADMIN])])
            ->controller(CustomerController::class)
            ->prefix('customers')
            ->name('customers.')
            ->group(function () {
                Route::get('{customer}/tickets', 'tickets')->name('tickets');
                Route::get('{customer}/history', 'history')->name('history');
                Route::post('{customer}/status', 'updateStatus')->name('status.update');
            });

        // =================================================================
        // REPORTS (Role-based)
        // =================================================================
        Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/generate', 'generate')->name('generate');
            Route::get('/export/{type}', 'export')->name('export');
            Route::get('/download/{filename}', 'download')->name('download');

            // Super Admin reports
            Route::middleware(['role:' . ROLE_SUPER_ADMIN])->group(function () {
                Route::get('/system', 'system')->name('system');
                Route::get('/companies-performance', 'companiesPerformance')->name('companies-performance');
            });

            // Company Admin reports
            Route::middleware(['role:' . ROLE_COMPANY_ADMIN])->group(function () {
                Route::get('/company', 'company')->name('company');
                Route::get('/agencies-performance', 'agenciesPerformance')->name('agencies-performance');
            });

            // Agency Admin reports
            Route::middleware(['role:' . ROLE_AGENCY_ADMIN])->group(function () {
                Route::get('/agency', 'agency')->name('agency');
                Route::get('/financial', 'financial')->name('financial');
                Route::get('/operational', 'operational')->name('operational');
                Route::get('/customer', 'customer')->name('customer');
            });
        });

        // =================================================================
        // NOTIFICATIONS
        // =================================================================
        Route::prefix('notifications')->name('notifications.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'notifications')->name('index');
            Route::get('/unread', 'unreadNotifications')->name('unread');
            Route::post('/{notification}/read', 'markAsRead')->name('read');
            Route::post('/read-all', 'markAllAsRead')->name('read-all');
            Route::delete('/{notification}', 'deleteNotification')->name('delete');
            Route::delete('/clear-all', 'clearAllNotifications')->name('clear-all');
        });

        // =================================================================
        // SETTINGS
        // =================================================================
        Route::prefix('settings')->name('settings.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'settings')->name('index');
            Route::post('/update-password', 'updatePassword')->name('update-password');
            Route::post('/update-preferences', 'updatePreferences')->name('update-preferences');
        });

        // =================================================================
        // ACTIVITY LOG (Admin roles)
        // =================================================================
        Route::middleware(['role:' . implode('|', [ROLE_SUPER_ADMIN, ROLE_COMPANY_ADMIN, ROLE_AGENCY_ADMIN])])
            ->get('/activity-log', [DashboardController::class, 'activityLog'])
            ->name('activity-log');
    });
});

// =========================================================================
// FALLBACK ROUTE (Must be the last route)
// =========================================================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// =========================================================================
// ROUTE CACHING HELPER (For production)
// =========================================================================
if (App::environment('production')) {
    Route::patterns([
        'id' => '[0-9]+',
        'company' => '[0-9]+',
        'agency' => '[0-9]+',
        'bus' => '[0-9]+',
        'trip' => '[0-9]+',
        'ticket' => '[0-9]+',
        'customer' => '[0-9]+',
        'notification' => '[0-9]+',
    ]);
}
