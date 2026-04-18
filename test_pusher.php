<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $pusher = new Pusher\Pusher('key', 'secret', 'app_id');
    echo "SUCCESS: Pusher class found and instantiated.\n";
} catch (\Throwable $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
    echo "Class exists: " . (class_exists('Pusher\Pusher') ? 'YES' : 'NO') . "\n";
}
