<?php

use App\Models\Company;
use App\Services\Intelligence\CommandCenterService;

$company = Company::first();
$service = app(CommandCenterService::class);

echo "--- COMMAND CENTER BACKEND VERIFICATION ---\n";
echo "Analyzing Company: {$company->name}\n\n";

$snapshot = $service->getSnapshot($company);

echo "TIMESTAMP: {$snapshot['timestamp']}\n";

echo "\n[FLEET STATS]\n";
foreach ($snapshot['fleet_stats'] as $k => $v) {
    echo "  - $k: $v\n";
}

echo "\n[REVENUE SNAPSHOT]\n";
foreach ($snapshot['revenue'] as $k => $v) {
    echo "  - $k: $v\n";
}

echo "\n[CRITICAL ALERTS] (Count: " . count($snapshot['critical_alerts']) . ")\n";
foreach ($snapshot['critical_alerts'] as $alert) {
    echo "  - [{$alert['type']}] {$alert['message']} ({$alert['meta']}) {$alert['level']}\n";
}

echo "\n[EFFICIENCY METRICS]\n";
foreach ($snapshot['efficiency'] as $k => $v) {
    echo "  - $k: $v\n";
}

echo "\n[INCIDENT TIMELINE] (Count: " . count($snapshot['timeline']) . ")\n";
foreach ($snapshot['timeline'] as $event) {
    echo "  - [{$event['time']}] {$event['event']} ({$event['meta']})\n";
}

echo "\nVERIFICATION COMPLETE\n";
