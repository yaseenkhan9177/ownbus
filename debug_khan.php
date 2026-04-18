<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

$email = 'khan@trader.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "USER NOT FOUND: $email\n";
    exit;
}

echo "User ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Role: " . $user->role . "\n";
echo "Company ID: " . ($user->company_id ?? 'NULL') . "\n";

if ($user->company_id) {
    $company = Company::find($user->company_id);
    if ($company) {
        echo "Company Name: " . $company->name . "\n";
        echo "Company Status: " . $company->status . "\n";
    } else {
        echo "Company with ID " . $user->company_id . " NOT FOUND in 'companies' table!\n";
    }
}

$test_pass = 'Password123!';
if (Hash::check($test_pass, $user->password)) {
    echo "Password '$test_pass' MATCHES: YES\n";
} else {
    echo "Password '$test_pass' MATCHES: NO\n";
    echo "Current Hash: " . $user->password . "\n";
}
