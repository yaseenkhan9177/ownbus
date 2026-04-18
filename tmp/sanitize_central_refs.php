<?php

$migrationsDir = 'database/migrations/tenant';
$centralTables = [
    'subscriptions',
    'subscription_plans',
    'subscription_invoices',
    'subscription_events',
    'subscription_change_requests',
    'agreement_versions',
    'agreement_acceptances',
    'users',
    'companies',
    'plans',
    'super_admin_requests',
    'admin_broadcasts',
    'global_settings',
    'feature_flags',
    'usage_metrics',
    'system_activities',
    'system_error_logs',
    'audit_logs',
    'activity_logs',
];

$files = scandir($migrationsDir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || is_dir("$migrationsDir/$file")) {
        continue;
    }

    $path = "$migrationsDir/$file";
    $content = file_get_contents($path);
    $originalContent = $content;

    foreach ($centralTables as $table) {
        // Match Schema::table('table_name', function...)
        // We want to replace the whole block with a comment if it's the only thing in up/down
        // or just comment it out.

        $pattern = "/Schema::table\(['\"]" . $table . "['\"].*?\{.*?\}\);/s";
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "// No-op: $table is a central table\n        ", $content);
            echo "Sanitized central table reference in $file: $table\n";
        }
    }

    if ($content !== $originalContent) {
        file_put_contents($path, $content);
    }
}
