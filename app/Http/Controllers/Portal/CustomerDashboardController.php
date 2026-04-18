<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Show customer dashboard
     */
    public function index(): View
    {
        $userId = auth()->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer) {
            return view('customer.dashboard', [
                'activeBookings' => collect(),
                'recentRentals' => collect(),
                'loyaltyPoints' => 0,
                'totalSpent' => 0,
                'error' => 'No customer profile linked to this account.'
            ]);
        }

        // Get active bookings
        $activeBookings = Rental::where('customer_id', $customer->id)
            ->whereIn('status', ['confirmed', 'assigned', 'dispatched', 'active', 'overdue'])
            ->with(['vehicle', 'driver'])
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();

        // Get recent rentals
        $recentRentals = Rental::where('customer_id', $customer->id)
            ->whereIn('status', ['completed', 'closed'])
            ->with(['vehicle', 'driver'])
            ->orderBy('end_date', 'desc')
            ->take(5)
            ->get();

        // Calculate loyalty points (simple: 1 point per $10 spent)
        $totalSpent = Rental::where('customer_id', $customer->id)
            ->where('payment_status', 'paid')
            ->sum('final_amount');

        $loyaltyPoints = floor($totalSpent / 10);

        return view('customer.dashboard', compact(
            'activeBookings',
            'recentRentals',
            'loyaltyPoints',
            'totalSpent'
        ));
    }

    /**
     * Show all rental history
     */
    public function rentals(Request $request)
    {
        $userId = auth()->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        if (!$customer) {
            return redirect()->route('portal.dashboard')->with('error', 'No customer profile found.');
        }

        $query = Rental::where('customer_id', $customer->id)
            ->with(['vehicle', 'driver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('start_date', '<=', $request->to_date);
        }

        $rentals = $query->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('customer.bookings.index', compact('rentals'));
    }

    /**
     * Download invoice for a rental
     */
    public function downloadInvoice(Rental $rental)
    {
        $userId = auth()->id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();

        // Ensure rental belongs to this customer
        if (!$customer || $rental->customer_id !== $customer->id) {
            abort(403);
        }

        // Ensure rental is paid
        if ($rental->payment_status !== 'paid') {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Invoice is only available for paid bookings.');
        }

        return view('customer.invoices.download', compact('rental'));
    }
}
