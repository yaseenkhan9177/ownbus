<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_audit_log_records_in_the_database()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->actingAs($user);

        $logger = app(AuditLogger::class);

        // 1. Log an action
        $logger->logRentalAction(123, $company->id, 'rental_approved', $user->id, ['test' => 'context']);

        // 2. Verify DB record
        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'action' => 'rental_approved',
            'entity_type' => 'Rental',
            'entity_id' => 123,
        ]);

        $log = AuditLog::where('action', 'rental_approved')->first();
        $this->assertEquals(['test' => 'context'], $log->metadata);
    }
}
