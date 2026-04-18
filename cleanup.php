<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Services\TenantService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$companies = Company::all();
$tenantService = app(TenantService::class);

foreach ($companies as $company) {
    echo "Processing {$company->database_name}...\n";
    $tenantService->switchDatabase($company->database_name);
    
    if (Schema::connection('tenant')->hasTable('user_notifications')) {
        Schema::connection('tenant')->disableForeignKeyConstraints();
        Schema::connection('tenant')->dropIfExists('user_notifications');
        Schema::connection('tenant')->enableForeignKeyConstraints();
        echo " - Dropped user_notifications table.\n";
    }

    $deleted = DB::connection('tenant')->table('migrations')
        ->where('migration', 'like', '%create_notifications_table%')
        ->delete();
        
    if ($deleted) {
        echo " - Removed migration record.\n";
    }
}

echo "Cleanup complete.\n";
