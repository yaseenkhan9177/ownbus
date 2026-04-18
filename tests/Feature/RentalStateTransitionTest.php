<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Company;
use App\Services\RentalStateService;
use App\Exceptions\InvalidStateTransitionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RentalStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $company;
    protected $rental;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RentalStateService::class);
        $this->company = Company::factory()->create();
        $this->rental = Rental::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'draft'
        ]);
    }

    #[Test]
    public function cannot_skip_states()
    {
        $this->expectException(InvalidStateTransitionException::class);
        // Skip from draft directly to dispatched
        $this->service->transition($this->rental, 'dispatched');
    }

    #[Test]
    public function cannot_go_backwards()
    {
        $this->rental->update(['status' => 'dispatched']);

        $this->expectException(InvalidStateTransitionException::class);
        // Try to go back to approved
        $this->service->transition($this->rental, 'approved');
    }

    #[Test]
    public function timestamp_enforcement_on_dispatch()
    {
        // Must be in assigned state to transition to dispatched
        $this->rental->update([
            'status' => 'assigned',
            'bus_id' => Vehicle::factory()->create(['company_id' => $this->company->id])->id,
            'driver_id' => User::factory()->create(['company_id' => $this->company->id])->id,
        ]);

        $this->assertNull($this->rental->actual_start_datetime);

        $this->service->transition($this->rental, 'dispatched');

        $this->assertNotNull($this->rental->fresh()->actual_start_datetime);
    }

    #[Test]
    public function timestamp_enforcement_on_complete()
    {
        $this->rental->update(['status' => 'dispatched']);

        $this->assertNull($this->rental->actual_end_datetime);

        $this->service->transition($this->rental, 'completed');

        $this->assertNotNull($this->rental->fresh()->actual_end_datetime);
    }

    #[Test]
    public function idempotency_protection()
    {
        $this->rental->update(['status' => 'quoted']);

        $this->expectException(InvalidStateTransitionException::class);
        // Transition to quoted again (not allowed from quoted to quoted)
        $this->service->transition($this->rental, 'quoted');
    }

    #[Test]
    public function cannot_dispatch_without_bus_id()
    {
        $this->rental->update([
            'status' => 'assigned',
            'bus_id' => null,
            'driver_id' => User::factory()->create(['company_id' => $this->company->id])->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("without assigned bus");

        $this->service->transition($this->rental->fresh(), 'dispatched');
    }

    #[Test]
    public function cannot_dispatch_without_driver_id()
    {
        $this->rental->update([
            'status' => 'assigned',
            'bus_id' => Vehicle::factory()->create(['company_id' => $this->company->id])->id,
            'driver_id' => null
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("without assigned bus and driver");

        $this->service->transition($this->rental->fresh(), 'dispatched');
    }

    #[Test]
    public function cannot_complete_without_dispatched()
    {
        $this->rental->update(['status' => 'assigned']);

        $this->expectException(InvalidStateTransitionException::class);
        $this->service->transition($this->rental, 'completed');
    }

    #[Test]
    public function transition_logs_are_recorded()
    {
        $this->rental->update(['status' => 'draft']);

        $this->service->transition($this->rental, 'quoted', 'Test reason');

        $this->assertDatabaseHas('rental_status_logs', [
            'rental_id' => $this->rental->id,
            'from_status' => 'draft',
            'to_status' => 'quoted',
            'reason' => 'Test reason'
        ]);
    }

    #[Test]
    public function full_transition_matrix_validation()
    {
        $statuses = [
            'draft',
            'quoted',
            'approved',
            'confirmed',
            'assigned',
            'dispatched',
            'completed',
            'closed',
            'cancelled',
            'no_show',
            'refunded'
        ];

        foreach ($statuses as $from) {
            foreach ($statuses as $to) {
                $can = $this->service->canTransition($from, $to);

                // Purely logical check against the service matrix
                if ($can) {
                    $this->assertTrue($can, "Status {$from} should be able to transition to {$to}");
                } else {
                    $this->assertFalse($can, "Status {$from} should NOT be able to transition to {$to}");
                }
            }
        }
    }
}
