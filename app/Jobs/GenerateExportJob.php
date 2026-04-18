<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Company;
use App\Notifications\ExportCompletedNotification;

// The Exports
use App\Exports\VehicleExport;
use App\Exports\RentalExport;
use App\Exports\InvoiceExport;

class GenerateExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $type;
    protected $format;
    protected $userId;
    protected $companyId;
    protected $filters;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, string $format, int $userId, int $companyId, array $filters = [])
    {
        $this->type = $type;
        $this->format = $format;
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::findOrFail($this->userId);
        $company = Company::findOrFail($this->companyId);
        
        $timestamp = now()->format('Y_m_d_His');
        $fileName = "{$this->type}_export_{$timestamp}.{$this->format}";
        $filePath = "exports/{$company->id}/{$fileName}";

        $exportObject = null;

        // Based on type, instantiate the correct logic
        if ($this->type === 'vehicles') {
            $exportObject = new VehicleExport($company, $this->filters);
        } elseif ($this->type === 'rentals') {
            $exportObject = new RentalExport($company, $this->filters['status'] ?? null);
        } elseif ($this->type === 'invoices') {
            // Suppose $this->filters has invoice IDs or just all invoices
            // Since InvoiceExport takes invoices collection, we need to load them
            $invoices = \App\Models\ContractInvoice::where('company_id', $company->id)->get();
            $exportObject = new InvoiceExport($company, $invoices);
        }

        if ($exportObject) {
            // Save the file to the public disk
            Excel::store($exportObject, $filePath, 'public');

            // Get URL
            $url = Storage::url($filePath);

            // Notify user
            $user->notify(new ExportCompletedNotification($url, $fileName));
        }
    }
}
