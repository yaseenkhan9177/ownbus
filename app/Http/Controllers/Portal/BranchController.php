<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $branches = Branch::orderBy('is_main', 'desc')->get();
        return view('portal.branches.index', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'currency' => 'required|string|size:3',
            'is_main' => 'boolean',
            // Manager validation
            'add_manager' => 'boolean',
            'manager_name' => 'required_if:add_manager,1|nullable|string|max:255',
            'manager_email' => 'required_if:add_manager,1|nullable|email|max:255|unique:users,email',
            'manager_password' => 'required_if:add_manager,1|nullable|string|min:8',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $company = $user->company;

        return DB::transaction(function () use ($validated, $company) {
            if ($validated['is_main'] ?? false) {
                Branch::query()->update(['is_main' => false]);
            }

            $branch = Branch::create($validated);

            if ($validated['add_manager'] ?? false) {
                $manager = User::create([
                    'company_id' => $company->id,
                    'name' => $validated['manager_name'],
                    'email' => $validated['manager_email'],
                    'password' => Hash::make($validated['manager_password']),
                    'role' => 'branch_manager',
                ]);

                // Assign role if you have a role system
                $role = Role::firstOrCreate(
                    ['company_id' => $company->id, 'name' => 'branch_manager'],
                    ['description' => 'Manages specific branch operations', 'is_system' => true]
                );

                $manager->roles()->attach($role->id);

                // Attach to branch
                $branch->users()->attach($manager->id, [
                    'role_id' => $role ? $role->id : null,
                    'is_active' => true,
                    'assigned_at' => now(),
                ]);
            }

            return redirect()->route('company.branches.index')->with('success', 'Station deployed successfully.');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'currency' => 'required|string|size:3',
            'is_main' => 'boolean',
        ]);

        if (($validated['is_main'] ?? false) && !$branch->is_main) {
            Branch::query()->update(['is_main' => false]);
        }

        $branch->update($validated);

        return redirect()->route('company.branches.index')->with('success', 'Station protocols updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::findOrFail($id);

        if ($branch->is_main) {
            return redirect()->back()->with('error', 'Cannot decommissioning the primary hub station.');
        }

        $branch->delete();

        return redirect()->route('company.branches.index')->with('success', 'Station decommissioned.');
    }
}
