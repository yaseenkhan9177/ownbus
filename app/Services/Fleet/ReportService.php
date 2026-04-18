<?php

namespace App\Services\Fleet;

use App\Models\Vehicle;
use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Generate Vehicle Performance Report.
     * Includes utilization, revenue, and maintenance costs.
     */
    public function generateVehiclePerformanceReport(int $companyId, $startDate, $endDate): Collection
    {
        return Vehicle::query()
            ->withSum(['rentals as total_revenue' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->where('status', 'completed');
            }], 'final_amount')
            ->withCount(['rentals as rental_count' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->where('status', 'completed');
            }])
            ->get()
            ->map(function ($vehicle) {
                return [
                    'vehicle_number' => $vehicle->vehicle_number,
                    'make_model' => $vehicle->make . ' ' . $vehicle->model,
                    'rental_count' => $vehicle->rental_count,
                    'total_revenue' => $vehicle->total_revenue ?? 0,
                    'status' => $vehicle->status,
                ];
            });
    }

    /**
     * Export data to CSV.
     */
    public function exportToCsv(Collection $data, array $headers, string $filename): StreamedResponse
    {
        $callback = function () use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($data as $row) {
                fputcsv($file, (array) $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
