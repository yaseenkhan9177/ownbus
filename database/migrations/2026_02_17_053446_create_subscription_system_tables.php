<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Starter, Growth, Enterprise
            $table->string('slug')->unique(); // starter, growth, enterprise
            $table->integer('version')->default(1); // For grandfathering
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->json('features'); // max_vehicles, max_users, max_branches, has_bi, has_api
            $table->boolean('is_active')->default(true);
            $table->integer('trial_days')->default(14);
            $table->integer('grace_period_days')->default(7);
            $table->timestamps();
            
            $table->index(['slug', 'version']);
        });

        // 2. Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('restrict');
            $table->integer('plan_version')->default(1);
            $table->enum('status', [
                'trialing',
                'active',
                'past_due',
                'grace',
                'canceled',
                'suspended',
                'incomplete'
            ])->default('trialing');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index('stripe_subscription_id');
        });

        // 3. Subscription Invoices
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('stripe_invoice_id')->nullable()->unique();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->integer('attempt_count')->default(0);
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index('stripe_invoice_id');
        });

        // 4. Subscription Events (Audit Trail)
        Schema::create('subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // created, renewed, failed, suspended, upgraded, downgraded, canceled
            $table->json('payload_json')->nullable(); // Full Stripe webhook payload
            $table->string('stripe_event_id')->nullable()->unique(); // For idempotency
            $table->timestamps();
            
            $table->index(['subscription_id', 'event_type']);
            $table->index('stripe_event_id');
        });

        // 5. Usage Metrics
        Schema::create('usage_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // vehicles, users, branches
            $table->integer('current_count')->default(0);
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['company_id', 'metric_type']);
        });

        // 6. Subscription Change Requests
        Schema::create('subscription_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->timestamp('scheduled_for')->nullable(); // Next billing cycle
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['subscription_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_change_requests');
        Schema::dropIfExists('usage_metrics');
        Schema::dropIfExists('subscription_events');
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
