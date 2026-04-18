<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleManagementController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $users = User::where('company_id', $user->company_id)
            ->with('branches')
            ->get();
        return view('portal.settings.roles.index', compact('users'));
    }

    public function invite(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'role' => 'required|string|in:admin,manager,staff,driver',
        ]);

        // Create user with random password (they should reset it)
        $password = Str::random(12);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => $validated['role'],
            'company_id' => $user->company_id,
        ]);

        // In a real app, send invitation email with $password
        // For now, just flash it
        return redirect()->back()->with('success', "User invited. Temporary password: $password");
    }

    public function update(Request $request, User $userToUpdate)
    {
        /** @var \App\Models\User $user */
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            abort(403);
        }

        // Ensure user belongs to same company
        if ($userToUpdate->company_id !== $currentUser->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userToUpdate->id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,manager,staff,driver,branch_manager',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $userToUpdate->update($updateData);

        return redirect()->back()->with('success', 'Operative protocols updated.');
    }
}
