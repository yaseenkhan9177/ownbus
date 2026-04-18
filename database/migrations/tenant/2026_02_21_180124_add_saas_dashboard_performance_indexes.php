<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $subscriptionIndexes = collect(Schema::getIndexes('subscriptions'))->pluck('name')->toArray();
        // No-op: subscriptions is a central table
        

        $invoiceIndexes = collect(Schema::getIndexes('subscription_invoices'))->pluck('name')->toArray();
        // No-op: subscription_invoices is a central table
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $subscriptionIndexes = collect(Schema::getIndexes('subscriptions'))->pluck('name')->toArray();
        // No-op: subscriptions is a central table
        

        $invoiceIndexes = collect(Schema::getIndexes('subscription_invoices'))->pluck('name')->toArray();
        // No-op: subscription_invoices is a central table
        
    }
};
