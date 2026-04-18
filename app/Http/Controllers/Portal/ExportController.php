<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\GenerateExportJob;

class ExportController extends Controller
{
    /**
     * Dispatch an export job to the queue.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:vehicles,rentals,invoices',
            'format' => 'required|in:xlsx,pdf',
            'filters' => 'nullable|array',
        ]);

        $company = auth()->user()->company;
        if (!$company) {
            abort(403, 'Must be associated with a company to export data.');
        }

        // Dispatch job
        GenerateExportJob::dispatch(
            $validated['type'],
            $validated['format'],
            auth()->id(),
            $company->id,
            $validated['filters'] ?? []
        );

        return back()->with('success', 'Export started! You will receive a notification when it is ready.');
    }
}
