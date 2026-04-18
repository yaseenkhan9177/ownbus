<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Repositories\RentalRepository;
use App\Services\RentalStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KanbanController extends Controller
{
    protected $rentalRepository;
    protected $stateService;
    protected $dispatchService;

    public function __construct(
        RentalRepository $rentalRepository,
        RentalStateService $stateService,
        \App\Services\Fleet\DispatchService $dispatchService
    ) {
        $this->rentalRepository = $rentalRepository;
        $this->stateService = $stateService;
        $this->dispatchService = $dispatchService;
    }

    protected function getCompanyOrAbort()
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'User is not associated with any company.');
        }
        return $company;
    }

    public function index()
    {
        $company = $this->getCompanyOrAbort();

        // Get rentals using repository with specific status filters would be ideal,
        // but for Kanban grouping we might need a custom method or just repository's getRentals with different status
        // To be efficient, we can fetch all active/recent rentals and group in memory or query per status.
        // Let's query per status to match repository pattern if getRentals supports it, or use repository to fetch a broader set.

        // Let's use a simpler approach for now: fetch all 'active' workflow rentals
        // We can leverage the repository's filtering if we want, or just add a helper in repo.
        // For now, let's just use the model directly but scoped to company, OR add a method to Repo.
        // Adding getRentalsByStatus to Repo is cleaner but for now consistent with previous logic:

        $stages = [
            'confirmed' => 'Pending',
            'assigned' => 'Driver Assigned',
            'dispatched' => 'Departed',
            'active' => 'In Progress',
            'completed' => 'Completed'
        ];

        $rentalsByStage = [];
        foreach ($stages as $status => $label) {
            // Using Repository's getRentals for each stage (might be slightly inefficient but consistent)
            // Ideally we'd have a getAllRentals for Kanban
            $filters = ['status' => $status, 'per_page' => 50]; // Limit per column
            $paginator = $this->rentalRepository->getRentals($company, $filters, 50);
            $rentalsByStage[$status] = $paginator->items();
        }

        return view('portal.kanban.index', compact('rentalsByStage', 'stages'));
    }

    /**
     * Handle AJAX status updates from Kanban drag-and-drop.
     */
    public function updateStatus(Request $request, Rental $rental)
    {
        // In a tenant-isolated database, any rental found belongs to the current tenant.

        $validated = $request->validate([
            'to_status' => 'required|string',
        ]);

        try {
            $this->stateService->transition($rental, $validated['to_status'], 'Kanban drag-and-drop');

            // broadcast(new \App\Events\RentalStatusUpdated($rental))->toOthers(); // Event not yet created

            return response()->json([
                'success' => true,
                'message' => 'Rental status updated to ' . ucfirst($validated['to_status']),
                'rental' => $rental->load(['customer', 'bus', 'driver'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get driver suggestions for a rental.
     */
    public function suggestDrivers(Rental $rental)
    {
        // No manual company_id check is needed once the connection is switched.

        $drivers = $this->dispatchService->suggestDrivers($rental);

        return response()->json([
            'success' => true,
            'drivers' => $drivers
        ]);
    }

    /**
     * Assign a driver to a rental.
     */
    public function assignDriver(Request $request, Rental $rental)
    {
        // No manual company_id check is needed once the connection is switched.

        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        try {
            $rental->driver_id = $validated['driver_id'];
            $rental->save();

            // Auto-transition to 'assigned' if currently 'confirmed'
            if ($rental->status === 'confirmed') {
                $this->stateService->transition($rental, 'assigned', 'Driver assigned via Kanban');
            }

            // broadcast(new \App\Events\RentalStatusUpdated($rental))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Driver assigned successfully',
                'rental' => $rental->load(['customer', 'bus', 'driver'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
