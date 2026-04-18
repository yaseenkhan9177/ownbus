<?php

$migrationsDir = 'database/migrations/tenant';
$files = scandir($migrationsDir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || is_dir("$migrationsDir/$file")) {
        continue;
    }

    $path = "$migrationsDir/$file";
    $content = file_get_contents($path);
    $originalContent = $content;

    // 1. Remove constrained() and its siblings in a chain
    $constraintMethods = [
        '/->constrained\([^)]*\)/',
        '/->onDelete\([^)]*\)/',
        '/->onUpdate\([^)]*\)/',
        '/->cascadeOnDelete\(\)/',
        '/->nullOnDelete\(\)/',
        '/->cascadeOnUpdate\(\)/',
    ];

    foreach ($constraintMethods as $pattern) {
        $content = preg_replace($pattern, '', $content);
    }

    // 2. Remove standalone foreign key definitions
    // $table->foreign('...')->...;
    $content = preg_replace('/\$table->foreign\([^;]*;\s*/', '', $content);

    // 3. Remove dropConstrainedForeignId
    $content = preg_replace('/\$table->dropConstrainedForeignId\([^;]*;\s*/', '', $content);

    // 4. Remove dropForeign
    $content = preg_replace('/\$table->dropForeign\([^;]*;\s*/', '', $content);

    if ($content !== $originalContent) {
        file_put_contents($path, $content);
        echo "Sanitized migration: $file\n";
    }
}
