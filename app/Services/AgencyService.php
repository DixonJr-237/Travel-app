<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\AgenceActivity;
use App\Models\Bus;
use App\Models\City;
use App\Models\Company;
use App\Models\Coordinate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AgencyService
{
    /**
     * Status constants
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Get all agencies with optional filters
     */
    public function getAllAgencies(array $filters = [], int $perPage = 10)
    {
        $query = Agence::with(['company', 'coordinates.city', 'user']);

        // Filter by company
        if (!empty($filters['company_id'])) {
            $query->where('id_company', $filters['company_id']);
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('id_city', $filters['city_id']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search by name or email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        return $query->withCount(['buses', 'users'])->orderBy('name')->paginate($perPage);
    }

    /**
     * Get agencies for company admin
     */
    public function getAgenciesForCompanyAdmin(int $companyId, array $filters = [], int $perPage = 10)
    {
        $query = Agence::with(['company', 'coordinates.city', 'user'])
            ->where('id_company', $companyId);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['city_id'])) {
            $query->where('id_city', $filters['city_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->withCount(['buses', 'users'])->orderBy('name')->paginate($perPage);
    }

    /**
     * Create a new agency with admin user
     */
    public function createAgency(array $data): Agence
    {
        return DB::transaction(function () use ($data) {
            // Create agency admin user
            $adminUser = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'agency_admin',
                'phone' => $data['phone'],
                'agency_id' => null,
                'company_id' => $data['id_company'],
            ]);

            // Create agency
            $agency = Agence::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'id_company' => $data['id_company'],
                'id_coord' => $data['id_coord'],
                'id_city' => $data['id_city'],
                'user_id' => $adminUser->user_id,
                'status' => self::STATUS_ACTIVE,
            ]);

            // Update user with agency_id
            $adminUser->update(['agency_id' => $agency->id_agence]);

            // Log activity
            $this->logActivity($agency, 'created', 'Agency created with admin user');

            return $agency;
        });
    }

    /**
     * Update an existing agency
     */
    public function updateAgency(Agence $agency, array $data): Agence
    {
        $oldData = $agency->toArray();

        $agency->update([
            'name' => $data['name'] ?? $agency->name,
            'email' => $data['email'] ?? $agency->email,
            'phone' => $data['phone'] ?? $agency->phone,
            'id_company' => $data['id_company'] ?? $agency->id_company,
            'id_coord' => $data['id_coord'] ?? $agency->id_coord,
            'id_city' => $data['id_city'] ?? $agency->id_city,
        ]);

        // Log changes
        $changes = array_diff_assoc($data, $oldData);
        if (!empty($changes)) {
            $this->logActivity($agency, 'updated', 'Agency details updated: ' . implode(', ', array_keys($changes)));
        }

        return $agency;
    }

    /**
     * Activate an agency
     */
    public function activateAgency(Agence $agency): bool
    {
        $result = $agency->update(['status' => self::STATUS_ACTIVE]);

        if ($result) {
            $this->logActivity($agency, 'status_changed', 'Agency activated');

            // Activate associated user
            if ($agency->user) {
                $agency->user->update(['is_active' => true]);
            }
        }

        return $result;
    }

    /**
     * Deactivate an agency
     */
    public function deactivateAgency(Agence $agency): bool
    {
        $result = $agency->update(['status' => self::STATUS_INACTIVE]);

        if ($result) {
            $this->logActivity($agency, 'status_changed', 'Agency deactivated');

            // Deactivate associated user
            if ($agency->user) {
                $agency->user->update(['is_active' => false]);
            }
        }

        return $result;
    }

    /**
     * Suspend an agency
     */
    public function suspendAgency(Agence $agency, string $reason = ''): bool
    {
        $result = $agency->update([
            'status' => self::STATUS_SUSPENDED,
            'suspension_reason' => $reason,
        ]);

        if ($result) {
            $this->logActivity($agency, 'suspended', 'Agency suspended. Reason: ' . $reason);

            // Deactivate associated user
            if ($agency->user) {
                $agency->user->update(['is_active' => false]);
            }
        }

        return $result;
    }

    /**
     * Delete an agency
     */
    public function deleteAgency(Agence $agency): bool
    {
        // Check if agency has buses
        if ($agency->buses()->exists()) {
            throw new \Exception('Cannot delete agency with existing buses. Delete buses first.');
        }

        return DB::transaction(function () use ($agency) {
            // Log activity before deletion
            $this->logActivity($agency, 'deleted', 'Agency deleted: ' . $agency->name);

            // Delete activities
            $agency->activities()->delete();

            // Delete agency admin user
            if ($agency->user) {
                $agency->user->delete();
            }

            return $agency->delete();
        });
    }

    /**
     * Bulk activate agencies
     */
    public function bulkActivate(array $agencyIds): int
    {
        $count = 0;
        foreach ($agencyIds as $id) {
            $agency = Agence::find($id);
            if ($agency && $agency->status !== self::STATUS_ACTIVE) {
                if ($this->activateAgency($agency)) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Bulk deactivate agencies
     */
    public function bulkDeactivate(array $agencyIds): int
    {
        $count = 0;
        foreach ($agencyIds as $id) {
            $agency = Agence::find($id);
            if ($agency && $agency->status !== self::STATUS_INACTIVE) {
                if ($this->deactivateAgency($agency)) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Bulk delete agencies
     */
    public function bulkDelete(array $agencyIds): array
    {
        $deleted = 0;
        $errors = [];

        foreach ($agencyIds as $id) {
            $agency = Agence::find($id);
            if (!$agency) {
                $errors[] = "Agency with ID {$id} not found.";
                continue;
            }

            try {
                if ($this->deleteAgency($agency)) {
                    $deleted++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to delete agency {$agency->name}: " . $e->getMessage();
            }
        }

        return [
            'deleted' => $deleted,
            'errors' => $errors,
        ];
    }

    /**
     * Get agency statistics
     */
    public function getAgencyStats(Agence $agency): array
    {
        $buses = $agency->buses;

        return [
            'total_buses' => $buses->count(),
            'active_buses' => $buses->where('status', 'active')->count(),
            'maintenance_buses' => $buses->where('status', 'maintenance')->count(),
            'inactive_buses' => $buses->where('status', 'inactive')->count(),
            'total_trips' => $buses->sum(function ($bus) {
                return $bus->trips->count();
            }),
            'upcoming_trips' => $buses->sum(function ($bus) {
                return $bus->trips->where('departure_date', '>=', now())->count();
            }),
            'total_staff' => $agency->users()->count(),
            'status' => $agency->status,
        ];
    }

    /**
     * Get comprehensive agency report
     */
    public function getAgencyReport(Agence $agency): array
    {
        $buses = $agency->buses()->with(['trips.tickets'])->get();

        $totalRevenue = $buses->sum(function ($bus) {
            return $bus->trips->sum(function ($trip) {
                return $trip->tickets->sum('price');
            });
        });

        $totalTrips = $buses->sum(function ($bus) {
            return $bus->trips->count();
        });

        $totalTickets = $buses->sum(function ($bus) {
            return $bus->trips->sum(function ($trip) {
                return $trip->tickets->count();
            });
        });

        return [
            'agency' => $agency,
            'total_revenue' => $totalRevenue,
            'total_trips' => $totalTrips,
            'total_tickets' => $totalTickets,
            'stats' => $this->getAgencyStats($agency),
        ];
    }

    /**
     * Get agency by ID with relationships
     */
    public function getAgencyById(int $id): ?Agence
    {
        return Agence::with(['company', 'coordinates.city.subRegion.region.country', 'user', 'buses', 'activities'])
            ->find($id);
    }

    /**
     * Get coordinates for a city
     */
    public function getCoordinatesForCity(int $cityId)
    {
        return Coordinate::where('id_city', $cityId)->get();
    }

    /**
     * Log agency activity
     */
    public function logActivity(Agence $agency, string $action, string $description): void
    {
        try {
            AgenceActivity::create([
                'id_agency' => $agency->id_agence,
                'action' => $action,
                'description' => $description,
                'user_id' => auth()->id() ?? $agency->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log agency activity: ' . $e->getMessage());
        }
    }

    /**
     * Get recent activities for an agency
     */
    public function getRecentActivities(Agence $agency, int $limit = 20)
    {
        return $agency->activities()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Export agencies to array
     */
    public function exportAgencies(array $filters = []): array
    {
        $query = Agence::with(['company', 'city']);

        if (!empty($filters['company_id'])) {
            $query->where('id_company', $filters['company_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->map(function ($agency) {
            return [
                'id' => $agency->id_agence,
                'name' => $agency->name,
                'email' => $agency->email,
                'phone' => $agency->phone,
                'company' => $agency->company->name ?? 'N/A',
                'city' => $agency->city->name ?? 'N/A',
                'status' => $agency->status,
                'created_at' => $agency->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Search agencies
     */
    public function searchAgencies(string $searchTerm, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Agence::with(['company', 'city'])
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Get agencies by status
     */
    public function getAgenciesByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Agence::with(['company', 'city'])
            ->where('status', $status)
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if agency can be deleted
     */
    public function canDeleteAgency(Agence $agency): array
    {
        $canDelete = true;
        $reasons = [];

        if ($agency->buses()->exists()) {
            $canDelete = false;
            $reasons[] = 'Agency has ' . $agency->buses()->count() . ' bus(es) assigned';
        }

        if ($agency->trips()->exists()) {
            $canDelete = false;
            $reasons[] = 'Agency has active or historical trips';
        }

        return [
            'can_delete' => $canDelete,
            'reasons' => $reasons,
        ];
    }
}
