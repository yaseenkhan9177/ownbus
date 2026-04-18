<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_protected_api()
    {
        // Example protected route from routes/api.php
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_access_api_with_token()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson(['id' => $this->user->id]);
    }

    #[Test]
    public function unauthorized_token_abilities_are_restricted()
    {
        // Token with ONLY read-access
        $token = $this->user->createToken('read-only-token', ['read-access'])->plainTextToken;

        // Resolution requires 'write-access'
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/monitor/anomalies/1/resolve');

        $response->assertStatus(403);
    }

    #[Test]
    public function rate_limiting_is_enforced()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        // Hit the endpoint multiple times rapidly
        for ($i = 0; $i < 10; $i++) {
            $this->withHeader('Authorization', 'Bearer ' . $token)
                ->getJson('/api/user');
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        // Check for rate limit headers (Standard in Laravel api middleware)
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }
}
