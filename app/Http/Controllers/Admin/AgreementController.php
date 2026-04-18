<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgreementVersion;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function index()
    {
        $agreements = AgreementVersion::latest()->get();
        return view('admin.agreements.index', compact('agreements'));
    }

    public function create()
    {
        return view('admin.agreements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|max:50|unique:agreement_versions,version',
            'content' => 'required|string',
        ]);

        // Deactivate all existing versions
        AgreementVersion::query()->update(['active' => false]);

        AgreementVersion::create([
            'version' => $request->version,
            'content' => $request->content,
            'active'  => true,
        ]);

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Agreement v' . $request->version . ' published successfully. It is now active for new registrations.');
    }

    public function edit(AgreementVersion $agreement)
    {
        return view('admin.agreements.edit', compact('agreement'));
    }

    public function update(Request $request, AgreementVersion $agreement)
    {
        $request->validate([
            'content' => 'required|string',
            'active'  => 'sometimes|boolean',
        ]);

        if ($request->boolean('active')) {
            AgreementVersion::query()->update(['active' => false]);
        }

        $agreement->update([
            'content' => $request->content,
            'active'  => $request->boolean('active'),
        ]);

        return redirect()->route('admin.agreements.index')
            ->with('success', 'Agreement updated successfully.');
    }

    public function setActive(AgreementVersion $agreement)
    {
        AgreementVersion::query()->update(['active' => false]);
        $agreement->update(['active' => true]);

        return back()->with('success', 'Agreement v' . $agreement->version . ' is now the active registration agreement.');
    }

    public function destroy(AgreementVersion $agreement)
    {
        if ($agreement->active) {
            return back()->with('error', 'Cannot delete the active agreement. Activate another version first.');
        }
        $agreement->delete();
        return back()->with('success', 'Agreement deleted.');
    }
}
