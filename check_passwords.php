<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$user = DB::table('users')->where('email', 'sadmin@gmail.com')->first();
$passwords = ['yaseen@2025', 'yaseen@2026', 'Password123!', 'admin@123', 'admin123', '123456789', 'qwerty'];

if ($user) {
    foreach ($passwords as $p) {
        if (Hash::check($p, $user->password)) {
            echo "Found: $p\n";
            exit;
        }
    }
    echo "Not found.\n";
} else {
    echo "User not found.\n";
}
