<?php

$modelsDir = 'app/Models';
$centralModels = [
    'Company.php',
    'Subscription.php',
    'SubscriptionPlan.php',
    'SubscriptionInvoice.php',
    'SubscriptionEvent.php',
    'SubscriptionChangeRequest.php',
    'AgreementVersion.php',
    'AgreementAcceptance.php',
    'User.php',
    'Plan.php',
    'SuperAdminRequest.php',
    'AdminBroadcast.php',
    'GlobalSetting.php',
    'FeatureFlag.php',
    'UsageMetric.php',
    'SystemActivity.php',
    'SystemErrorLog.php',
    'AuditLog.php',
    'ActivityLog.php',
];

$files = scandir($modelsDir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || is_dir("$modelsDir/$file")) {
        continue;
    }

    if (in_array($file, $centralModels)) {
        echo "Skipping central model: $file\n";
        continue;
    }

    $path = "$modelsDir/$file";
    $content = file_get_contents($path);
    $originalContent = $content;

    // 1. Remove Trait imports
    $content = preg_replace('/use App\\\\Models\\\\Traits\\\\BelongsToCompany;\s*/', '', $content);
    $content = preg_replace('/use App\\\\Models\\\\Traits\\\\ScopedByCompany;\s*/', '', $content);

    // 2. Remove Traits from use statement in class
    // Handles: use HasFactory, BelongsToCompany; or use BelongsToCompany;
    $content = preg_replace('/use ([^;]*)BelongsToCompany,\s*/', 'use $1', $content);
    $content = preg_replace('/use ([^;]*),\s*BelongsToCompany/', 'use $1', $content);
    $content = preg_replace('/use BelongsToCompany;\s*/', '', $content);

    $content = preg_replace('/use ([^;]*)ScopedByCompany,\s*/', 'use $1', $content);
    $content = preg_replace('/use ([^;]*),\s*ScopedByCompany/', 'use $1', $content);
    $content = preg_replace('/use ScopedByCompany;\s*/', '', $content);

    // Clean up trailing commas in use lists like "use HasFactory, ;"
    $content = preg_replace('/use ([^;]+),\s*;\s*/', 'use $1;', $content);

    // 3. Add protected $connection = 'tenant';
    if (!str_contains($content, 'protected $connection = \'tenant\'')) {
        $content = preg_replace('/class [^ ]+ extends Model\s*\{/', "$0\n    protected \$connection = 'tenant';", $content);
    }

    // 4. Remove company_id from $fillable
    $content = preg_replace('/(\s*)\'company_id\',/', '', $content);

    if ($content !== $originalContent) {
        file_put_contents($path, $content);
        echo "Refactored tenant model: $file\n";
    }
}
