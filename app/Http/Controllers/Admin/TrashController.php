<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\ContractInvoice;
use Illuminate\Support\Facades\Gate;

class TrashController extends Controller
{
    private array $modules = [
        'vehicles' => Vehicle::class,
        'rentals' => Rental::class,
        'customers' => Customer::class,
        'contracts' => Contract::class,
        'invoices' => ContractInvoice::class,
    ];

    public function index(Request $request)
    {
        // Load all trashed items
        $trashed = [];

        foreach ($this->modules as $key => $modelClass) {
            $query = app($modelClass)::onlyTrashed()->latest('deleted_at');
            $trashed[$key] = $query->paginate(20, ['*'], $key . '_page');
        }

        return view('admin.trash.index', compact('trashed'));
    }

    public function restore(Request $request, $module, $id)
    {
        if (!array_key_exists($module, $this->modules)) {
            abort(404);
        }

        $modelClass = $this->modules[$module];
        $item = app($modelClass)::onlyTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', "Item beautifully restored to the $module active list!");
    }

    public function forceDelete(Request $request, $module, $id)
    {
        // Only Super Admin can force delete
        // If the system uses spatie, or a simple middleware 'isSuperAdmin', we check it:
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Force delete is restricted to Super Admin only.');
        }

        if (!array_key_exists($module, $this->modules)) {
            abort(404);
        }

        $modelClass = $this->modules[$module];
        $item = app($modelClass)::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return back()->with('success', "Item permanently deleted.");
    }
}
