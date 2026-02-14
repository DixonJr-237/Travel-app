<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\Trip;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateReportsCommand extends Command
{
    protected $signature = 'reports:generate
                            {type : Type of report (daily, weekly, monthly, yearly)}
                            {--company= : Company ID}
                            {--agency= : Agency ID}
                            {--format=pdf : Output format (pdf, excel, csv)}
                            {--email= : Email to send report to}';

    protected $description = 'Generate automated reports';

    public function handle()
    {
        $type = $this->argument('type');
        $companyId = $this->option('company');
        $agencyId = $this->option('agency');
        $format = $this->option('format');
        $email = $this->option('email');

        $this->info("Generating {$type} report...");

        try {
            $data = $this->generateReportData($type, $companyId, $agencyId);
            $filename = $this->saveReport($data, $type, $format);

            $this->info("Report generated successfully: {$filename}");

            if ($email) {
                $this->sendReportByEmail($email, $filename, $type);
                $this->info("Report sent to: {$email}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error generating report: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function generateReportData($type, $companyId = null, $agencyId = null)
    {
        $startDate = null;
        $endDate = now();

        switch ($type) {
            case 'daily':
                $startDate = now()->startOfDay();
                break;
            case 'weekly':
                $startDate = now()->startOfWeek();
                break;
            case 'monthly':
                $startDate = now()->startOfMonth();
                break;
            case 'yearly':
                $startDate = now()->startOfYear();
                break;
        }

        $query = Ticket::query()
            ->with(['trip.bus.agency', 'customer'])
            ->whereBetween('purchase_date', [$startDate, $endDate]);

        if ($companyId) {
            $query->whereHas('trip.bus.agency', function($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        if ($agencyId) {
            $query->whereHas('trip.bus', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }

        $tickets = $query->get();
        $totalRevenue = $tickets->sum('price');
        $totalTickets = $tickets->count();

        // Get trip statistics
        $tripQuery = Trip::query()
            ->whereBetween('departure_date', [$startDate, $endDate]);

        if ($companyId) {
            $tripQuery->whereHas('bus.agency', function($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        if ($agencyId) {
            $tripQuery->whereHas('bus', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }

        $totalTrips = $tripQuery->count();
        $completedTrips = $tripQuery->where('status', 'completed')->count();

        return [
            'type' => $type,
            'period' => [
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate->format('Y-m-d H:i:s'),
            ],
            'statistics' => [
                'total_revenue' => $totalRevenue,
                'total_tickets' => $totalTickets,
                'total_trips' => $totalTrips,
                'completed_trips' => $completedTrips,
                'average_ticket_price' => $totalTickets > 0 ? $totalRevenue / $totalTickets : 0,
            ],
            'top_routes' => $this->getTopRoutes($startDate, $endDate, $companyId, $agencyId),
            'revenue_by_day' => $this->getRevenueByDay($startDate, $endDate, $companyId, $agencyId),
            'tickets' => $tickets,
        ];
    }

    private function getTopRoutes($startDate, $endDate, $companyId = null, $agencyId = null)
    {
        $query = Ticket::query()
            ->selectRaw('journeys.name, COUNT(*) as ticket_count, SUM(tickets.price) as revenue')
            ->join('trips', 'tickets.trip_id', '=', 'trips.trip_id')
            ->join('journeys', 'trips.journey_id', '=', 'journeys.journey_id')
            ->whereBetween('tickets.purchase_date', [$startDate, $endDate])
            ->groupBy('journeys.journey_id', 'journeys.name')
            ->orderBy('revenue', 'desc')
            ->limit(10);

        if ($companyId) {
            $query->whereHas('trip.bus.agency', function($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        if ($agencyId) {
            $query->whereHas('trip.bus', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }

        return $query->get();
    }

    private function getRevenueByDay($startDate, $endDate, $companyId = null, $agencyId = null)
    {
        $query = Ticket::query()
            ->selectRaw('DATE(purchase_date) as date, SUM(price) as revenue')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        if ($companyId) {
            $query->whereHas('trip.bus.agency', function($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        if ($agencyId) {
            $query->whereHas('trip.bus', function($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });
        }

        return $query->get();
    }

    private function saveReport($data, $type, $format)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "report_{$type}_{$timestamp}.{$format}";
        $path = "reports/{$filename}";

        switch ($format) {
            case 'pdf':
                $pdf = Pdf::loadView('reports.template', $data);
                Storage::put($path, $pdf->output());
                break;
            case 'excel':
                // Implement Excel export
                break;
            case 'csv':
                $this->generateCsv($data, $path);
                break;
        }

        return $filename;
    }

    private function generateCsv($data, $path)
    {
        $handle = fopen(storage_path("app/{$path}"), 'w');

        // Write headers
        fputcsv($handle, ['Date', 'Revenue', 'Tickets', 'Trips']);

        // Write data
        foreach ($data['revenue_by_day'] as $row) {
            fputcsv($handle, [
                $row->date,
                $row->revenue,
                // Add other data as needed
            ]);
        }

        fclose($handle);
    }

    private function sendReportByEmail($email, $filename, $type)
    {
        // Implement email sending logic
        // Use Laravel Mail facade
    }
}
