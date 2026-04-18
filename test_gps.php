<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vehicle;

// Find first vehicle to use for testing
$vehicle = Vehicle::first();
if (!$vehicle) {
    die("No vehicles found in the database to test with.\n");
}

$deviceToken = 'test_device_' . $vehicle->id;

// Update the vehicle with a test device token
$vehicle->update(['telematics_device_id' => $deviceToken]);
echo "Updated Vehicle #{$vehicle->id} with telematics_device_id: {$deviceToken}\n";

// Coordinates for Dubai, for example
$lat = 25.2048 + (rand(-100, 100) / 10000);
$lng = 55.2708 + (rand(-100, 100) / 10000);
$speed = rand(40, 100);

echo "Sending Ping: Lat: {$lat}, Lng: {$lng}, Speed: {$speed}\n";

$data = [
    'device_token' => $deviceToken,
    'lat' => $lat,
    'lng' => $lng,
    'speed' => $speed,
    'heading' => rand(0, 360),
    'accuracy' => 5.0,
];

$ch = curl_init('http://127.0.0.1:8000/api/v1/gps/ping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$decoded = json_decode($response, true);
if ($decoded && isset($decoded['message'])) {
    echo "Message: {$decoded['message']}\n";
    echo "File: {$decoded['file']}\n";
    echo "Line: {$decoded['line']}\n";
} else {
    echo "Response: {$response}\n";
}
