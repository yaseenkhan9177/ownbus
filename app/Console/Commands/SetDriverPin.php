<?php

namespace App\Console\Commands;

use App\Models\Driver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetDriverPin extends Command
{
    protected $signature   = 'driver:set-pin {phone} {pin}';
    protected $description = 'Set a 4-digit login PIN for a driver identified by phone number';

    public function handle(): void
    {
        $phone = $this->argument('phone');
        $pin   = $this->argument('pin');

        if (strlen($pin) !== 4 || !ctype_digit($pin)) {
            $this->error('PIN must be exactly 4 digits (e.g. 1234).');
            return;
        }

        $driver = Driver::where('phone', $phone)->first();

        if (!$driver) {
            $this->error("No driver found with phone: {$phone}");
            return;
        }

        $driver->update(['pin_hash' => Hash::make($pin)]);

        $this->info("✅ PIN set for driver: {$driver->name} ({$driver->phone})");
        $this->line("   They can now log in at: /driver/login");
    }
}
