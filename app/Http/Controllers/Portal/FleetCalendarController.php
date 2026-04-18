<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FleetCalendarController extends Controller
{
    public function index()
    {
        $company  = Auth::user()->company;
        $vehicles = Vehicle::orderBy('name')
            ->get(['id', 'name', 'vehicle_number']);

        return view('portal.fleet.calendar', compact('vehicles'));
    }

    /**
     * JSON endpoint for FullCalendar resource events.
     */
    public function events(Request $request)
    {
        $company = Auth::user()->company;
        $start   = $request->query('start', now()->startOfMonth()->toDateString());
        $end     = $request->query('end', now()->endOfMonth()->toDateString());

        // Build FullCalendar resource list
        $vehicles = Vehicle::get(['id', 'name', 'vehicle_number'])
            ->map(fn($v) => [
                'id'    => (string) $v->id,
                'title' => "{$v->vehicle_number} – {$v->name}",
            ]);

        // Fetch rentals in range
        $rentals = Rental::where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->with('customer:id,first_name,last_name,company_name,type')
            ->get(['id', 'uuid', 'vehicle_id', 'customer_id', 'start_date', 'end_date', 'status', 'final_amount']);

        $colorMap = [
            'active'    => ['bg' => '#059669', 'border' => '#047857'], // emerald
            'overdue'   => ['bg' => '#dc2626', 'border' => '#b91c1c'], // red
            'completed' => ['bg' => '#6366f1', 'border' => '#4f46e5'], // indigo
            'pending'   => ['bg' => '#d97706', 'border' => '#b45309'], // amber
        ];

        $events = $rentals->map(function ($rental) use ($colorMap) {
            $colors = $colorMap[$rental->status] ?? ['bg' => '#64748b', 'border' => '#475569'];
            $customer = $rental->customer;
            $customerName = $customer
                ? ($customer->type === 'corporate' ? $customer->company_name : "{$customer->first_name} {$customer->last_name}")
                : 'N/A';

            return [
                'id'              => $rental->id,
                'resourceId'      => (string) $rental->vehicle_id,
                'title'           => $customerName,
                'start'           => $rental->start_date->toDateString(),
                'end'             => $rental->end_date->addDay()->toDateString(), // FullCalendar end is exclusive
                'backgroundColor' => $colors['bg'],
                'borderColor'     => $colors['border'],
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'status'  => $rental->status,
                    'amount'  => 'AED ' . number_format($rental->final_amount ?? 0, 2),
                    'uuid'    => substr($rental->uuid, 0, 8),
                ],
            ];
        });

        return response()->json([
            'resources' => $vehicles,
            'events'    => $events,
        ]);
    }
}
