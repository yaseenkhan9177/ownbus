<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdminRequest;
use App\Models\User;

class SuperAdminRequestController extends Controller
{
    public function index()
    {
        $requests = SuperAdminRequest::latest()->get();
        return view('admin.super-admin-requests', compact('requests'));
    }

    public function approve($id)
    {
        $adminRequest = SuperAdminRequest::findOrFail($id);

        if ($adminRequest->status !== 'pending') {
            return back()->with('error', 'Request already processed.');
        }

        // Create the user
        User::create([
            'name' => $adminRequest->name,
            'email' => $adminRequest->email,
            'password' => $adminRequest->password, // Password was hashed during request
            'role' => 'super_admin',
        ]);

        $adminRequest->update(['status' => 'approved']);

        return back()->with('success', "Super Admin access granted to {$adminRequest->name}.");
    }

    public function reject($id)
    {
        $adminRequest = SuperAdminRequest::findOrFail($id);

        if ($adminRequest->status !== 'pending') {
            return back()->with('error', 'Request already processed.');
        }

        $adminRequest->update(['status' => 'rejected']);

        return back()->with('success', "Super Admin access rejected for {$adminRequest->name}.");
    }
}
