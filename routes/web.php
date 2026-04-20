<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuperAdminAuthController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/demo-login', function () {
    $user = User::where('email', 'test@example.com')->first() ?? User::first();
    if ($user) {
        Auth::login($user);
        return redirect('/dashboard');
    }
    return redirect('/login')->with('error', 'Demo user not found.');
})->name('demo.login');

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// ============================================
// COMPANY REGISTRATION FLOW
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [App\Http\Controllers\Auth\RegistrationController::class, 'showForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegistrationController::class, 'process'])->name('register.process');
    Route::get('/agreement/{version}', [App\Http\Controllers\Auth\RegistrationController::class, 'showAgreement'])->name('agreement.show');
});



// ============================================
// CUSTOMER PORTAL ROUTES (Public & Customer)
// ============================================

// Public routes (no authentication required)
Route::prefix('portal')->name('portal.')->group(function () {
    // Vehicle browsing (public)
    Route::get('/vehicles', [App\Http\Controllers\Portal\VehicleBrowseController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/{vehicle}', [App\Http\Controllers\Portal\VehicleBrowseController::class, 'show'])->name('vehicles.show');
    Route::post('/vehicles/{vehicle}/check-availability', [App\Http\Controllers\Portal\VehicleBrowseController::class, 'checkAvailability'])->name('vehicles.check-availability');

    // Customer login/register
    Route::get('/login', function () {
        return view('portal.auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('portal.auth.register');
    })->name('register');

    // Customer-only routes (requires authentication)
    Route::middleware('customer.auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Portal\CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/rentals', [App\Http\Controllers\Portal\CustomerDashboardController::class, 'rentals'])->name('rentals.index');
        Route::get('/rentals/{rental}/invoice', [App\Http\Controllers\Portal\CustomerDashboardController::class, 'downloadInvoice'])->name('rentals.invoice');

        // Booking
        Route::get('/book/{vehicle}', [App\Http\Controllers\Portal\BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings/calculate-price', [App\Http\Controllers\Portal\BookingController::class, 'calculatePrice'])->name('bookings.calculate-price');
        Route::post('/bookings', [App\Http\Controllers\Portal\BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{rental}', [App\Http\Controllers\Portal\BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{rental}/cancel', [App\Http\Controllers\Portal\BookingController::class, 'cancel'])->name('bookings.cancel');

        // Payments
        Route::get('/payments/{rental}', [App\Http\Controllers\Portal\PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{rental}/success', [App\Http\Controllers\Portal\PaymentController::class, 'success'])->name('payments.success');
        Route::get('/payments/{rental}/cancel', [App\Http\Controllers\Portal\PaymentController::class, 'cancel'])->name('payments.cancel');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Portal\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Portal\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Portal\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [App\Http\Controllers\Portal\NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Settings - Notification Preferences
        Route::get('/settings/notifications', [App\Http\Controllers\Portal\NotificationPreferencesController::class, 'edit'])->name('settings.notifications');
        Route::put('/settings/notifications', [App\Http\Controllers\Portal\NotificationPreferencesController::class, 'update'])->name('settings.notifications.update');
    });

    // Stripe Webhook (public, outside middleware)
    Route::post('/webhook/stripe', [App\Http\Controllers\Portal\PaymentController::class, 'webhook'])->name('webhook.stripe');
});

Route::get('/quote/check', [App\Http\Controllers\QuoteController::class, 'checkAvailability'])->name('quote.check');
Route::post('/quote/book', [App\Http\Controllers\QuoteController::class, 'storeBooking'])->name('quote.book');

// Redirection route
Route::get('/dashboard', App\Http\Controllers\DashboardRedirectController::class)->middleware(['auth'])->name('dashboard');

// ============================================
// SUPER ADMIN REGISTRATION ROUTES (Controlled via PIN)
// ============================================
Route::prefix('super-admin')->name('super-admin.')->middleware(['web', 'guest'])->group(function () {
    Route::get('/register/pin', [SuperAdminAuthController::class, 'showPinForm'])->name('pin');
    Route::post('/register/pin', [SuperAdminAuthController::class, 'verifyPin'])->name('pin.verify');
    Route::get('/register', [SuperAdminAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [SuperAdminAuthController::class, 'register'])->name('register.store');
});

// ============================================
// SUPER ADMIN PORTAL ROUTES (SaaS Control)
// ============================================
Route::post('/impersonation/leave', [\App\Http\Controllers\Admin\CompanyController::class, 'leaveImpersonation'])->name('impersonation.leave')->middleware(['auth']);

Route::prefix('admin')->name('admin.')->middleware(['auth', 'isSuperAdmin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Access Requests
    Route::get('/super-admin-requests', [\App\Http\Controllers\Admin\SuperAdminRequestController::class, 'index'])->name('requests.index');
    Route::post('/super-admin-requests/{id}/approve', [\App\Http\Controllers\Admin\SuperAdminRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/super-admin-requests/{id}/reject', [\App\Http\Controllers\Admin\SuperAdminRequestController::class, 'reject'])->name('requests.reject');

    Route::post('companies/{company}/impersonate', [\App\Http\Controllers\Admin\CompanyController::class, 'impersonate'])->name('companies.impersonate');
    Route::post('companies/{company}/approve', [\App\Http\Controllers\Admin\CompanyController::class, 'approve'])->name('companies.approve');
    Route::post('companies/{company}/toggle-status', [\App\Http\Controllers\Admin\CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
    Route::post('companies/{company}/grant-license', [\App\Http\Controllers\Admin\CompanyController::class, 'grantLicense'])->name('companies.grant-license');
    Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class);
    Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class);

    // Support Tickets
    Route::get('support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('support.index');
    Route::get('support/{ticket}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('support.show');
    Route::post('support/{ticket}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('support.reply');
    Route::patch('support/{ticket}/status', [\App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('support.update-status');

    // Explicitly defined single-action controllers
    Route::get('billing', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('billing.index');
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('system', [\App\Http\Controllers\Admin\SystemController::class, 'index'])->name('system.index');
    // Audit Logs
    Route::get('audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Trash System
    Route::get('trash', [\App\Http\Controllers\Admin\TrashController::class, 'index'])->name('trash.index');
    Route::post('trash/{module}/{id}/restore', [\App\Http\Controllers\Admin\TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('trash/{module}/{id}/force-delete', [\App\Http\Controllers\Admin\TrashController::class, 'forceDelete'])->name('trash.force-delete');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\GlobalSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\GlobalSettingController::class, 'update'])->name('settings.update');

    // Admin Broadcasts (keeping the existing logic explicit)
    Route::get('/broadcasts', [App\Http\Controllers\Admin\BroadcastController::class, 'index'])->name('broadcasts.index');
    Route::post('/broadcasts', [App\Http\Controllers\Admin\BroadcastController::class, 'store'])->name('broadcasts.store');
    Route::delete('/broadcasts/{broadcast}', [App\Http\Controllers\Admin\BroadcastController::class, 'destroy'])->name('broadcasts.destroy');
    // Agreement / Terms Management
    Route::get('/agreements', [\App\Http\Controllers\Admin\AgreementController::class, 'index'])->name('agreements.index');
    Route::get('/agreements/create', [\App\Http\Controllers\Admin\AgreementController::class, 'create'])->name('agreements.create');
    Route::post('/agreements', [\App\Http\Controllers\Admin\AgreementController::class, 'store'])->name('agreements.store');
    Route::get('/agreements/{agreement}/edit', [\App\Http\Controllers\Admin\AgreementController::class, 'edit'])->name('agreements.edit');
    Route::put('/agreements/{agreement}', [\App\Http\Controllers\Admin\AgreementController::class, 'update'])->name('agreements.update');
    Route::post('/agreements/{agreement}/activate', [\App\Http\Controllers\Admin\AgreementController::class, 'setActive'])->name('agreements.activate');
    Route::delete('/agreements/{agreement}', [\App\Http\Controllers\Admin\AgreementController::class, 'destroy'])->name('agreements.destroy');
});


// ============================================
// SUBSCRIPTION BILLING
// ============================================
Route::prefix('subscription')->name('subscription.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\SubscriptionController::class, 'show'])->name('show');
    Route::get('/upgrade', [\App\Http\Controllers\SubscriptionController::class, 'upgrade'])->name('upgrade');
    Route::post('/checkout', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('checkout');
    Route::get('/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');
    Route::get('/expired', [\App\Http\Controllers\SubscriptionController::class, 'expired'])->name('expired');
    Route::get('/plans', [\App\Http\Controllers\SubscriptionController::class, 'plans'])->name('plans');
});

// ============================================
// COMPANY OWNER PORTAL ROUTES (Fleet Client)
// ============================================
Route::prefix('company')->name('company.')->middleware(['auth', \App\Http\Middleware\IsCompanyOwner::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Portal\CompanyDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/utilization-trend', [App\Http\Controllers\Portal\CompanyDashboardController::class, 'getUtilizationTrend'])->name('dashboard.utilization-trend');

    // Operational Intelligence
    Route::get('/intelligence', [App\Http\Controllers\Portal\ExecutiveDashboardController::class, 'index'])->name('intelligence.executive');
    Route::post('/intelligence/calculate-rate', [\App\Http\Controllers\Api\Intelligence\PricingApiController::class, 'calculate'])->name('intelligence.calculate-rate');

    // SaaS Intelligence Features
    Route::get('/pricing', [App\Http\Controllers\Portal\PricingController::class, 'index'])->name('pricing.index');
    Route::post('/pricing/calculate', [App\Http\Controllers\Portal\PricingController::class, 'calculate'])->name('pricing.calculate');
    Route::get('/maintenance/predictions', [App\Http\Controllers\Portal\MaintenanceController::class, 'predictions'])->name('maintenance.predictions');
    Route::post('/maintenance/predictions/run', [App\Http\Controllers\Portal\MaintenanceController::class, 'runAnalysis'])->name('maintenance.predictions.run');

    // Phase 7M: Enterprise Command Center
    Route::get('/command-center', [App\Http\Controllers\Portal\CommandCenterController::class, 'index'])->name('command-center');
    Route::get('/api/command-center/snapshot', [App\Http\Controllers\Portal\CommandCenterController::class, 'apiSnapshot'])->name('command-center.api');

    // Kanban Operations
    Route::get('/kanban', [App\Http\Controllers\Portal\KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/{rental}/status', [App\Http\Controllers\Portal\KanbanController::class, 'updateStatus'])->name('kanban.update-status');
    Route::get('/kanban/{rental}/suggest-drivers', [App\Http\Controllers\Portal\KanbanController::class, 'suggestDrivers'])->name('kanban.suggest-drivers');
    Route::post('/kanban/{rental}/assign-driver', [App\Http\Controllers\Portal\KanbanController::class, 'assignDriver'])->name('kanban.assign-driver');

    // Fleet Management (Operations Manager, Owner)
    Route::middleware(['role:Operations Manager|Owner'])->group(function () {
        Route::get('fleet/analytics', [App\Http\Controllers\FleetAnalyticsController::class, 'index'])->name('fleet.analytics');
        Route::get('fleet/calendar', [App\Http\Controllers\Portal\FleetCalendarController::class, 'index'])->name('fleet.calendar');
        Route::get('fleet/calendar/events', [App\Http\Controllers\Portal\FleetCalendarController::class, 'events'])->name('fleet.calendar.events');
        Route::get('telematics/dashboard', [App\Http\Controllers\Admin\TelematicsDashboardController::class, 'index'])->name('telematics.dashboard');
        // Enterprise Maintenance Engine
        Route::get('maintenance/schedule', [App\Http\Controllers\Portal\MaintenanceController::class, 'schedule'])->name('maintenance.schedule');
        Route::post('maintenance/{maintenance}/complete', [App\Http\Controllers\Portal\MaintenanceController::class, 'complete'])->name('maintenance.complete');
        Route::post('maintenance/{maintenance}/cancel', [App\Http\Controllers\Portal\MaintenanceController::class, 'cancel'])->name('maintenance.cancel');
        Route::resource('maintenance', App\Http\Controllers\Portal\MaintenanceController::class);
        Route::resource('fleet', App\Http\Controllers\Portal\FleetController::class)->parameters(['fleet' => 'vehicle']);
    });
    Route::resource('branches', \App\Http\Controllers\Portal\BranchController::class);

    // Rentals (Booking Clerk, Accountant, Owner)
    Route::middleware(['role:Booking Clerk|Accountant|Owner'])->group(function () {
        Route::get('/rentals/contract', [\App\Http\Controllers\RentalController::class, 'contractForm'])->name('rentals.contract');
        Route::post('/rentals/contract', [\App\Http\Controllers\RentalController::class, 'storeContract'])->name('rentals.contract.store');
        Route::post('rentals/{rental}/transition', [\App\Http\Controllers\RentalController::class, 'transition'])->name('rentals.transition');
        Route::post('/api/rental/calculate-price', [\App\Http\Controllers\Api\RentalPriceController::class, 'calculate'])->name('api.rental.price');
        Route::resource('rentals', \App\Http\Controllers\RentalController::class);
    });

    // Trips (operational trip records)
    Route::middleware(['role:Operations Manager|Owner|Booking Clerk'])->group(function () {
        Route::get('/trips', [\App\Http\Controllers\Portal\TripController::class, 'index'])->name('trips.index');
        Route::get('/trips/{trip}', [\App\Http\Controllers\Portal\TripController::class, 'show'])->name('trips.show');
        Route::patch('/trips/{trip}/cancel', [\App\Http\Controllers\Portal\TripController::class, 'cancel'])->name('trips.cancel');
        
        Route::get('/fuel', [\App\Http\Controllers\Portal\FuelController::class, 'index'])->name('fuel.index');
        Route::post('/fuel', [\App\Http\Controllers\Portal\FuelController::class, 'store'])->name('fuel.store');
        Route::get('/fuel/{fuel}', [\App\Http\Controllers\Portal\FuelController::class, 'show'])->name('fuel.show');
        Route::delete('/fuel/{fuel}', [\App\Http\Controllers\Portal\FuelController::class, 'destroy'])->name('fuel.destroy');

        Route::get('/breakdowns', [\App\Http\Controllers\Portal\BreakdownController::class, 'index'])->name('breakdowns.index');
        Route::get('/breakdowns/{breakdown}', [\App\Http\Controllers\Portal\BreakdownController::class, 'show'])->name('breakdowns.show');
        Route::patch('/breakdowns/{breakdown}/acknowledge', [\App\Http\Controllers\Portal\BreakdownController::class, 'acknowledge'])->name('breakdowns.acknowledge');
        Route::patch('/breakdowns/{breakdown}/resolve', [\App\Http\Controllers\Portal\BreakdownController::class, 'resolve'])->name('breakdowns.resolve');
    });

    // Drivers & Customers
    Route::post('drivers/{driver}/toggle-status', [\App\Http\Controllers\DriverController::class, 'toggleStatus'])->name('drivers.toggle-status');
    Route::resource('drivers', \App\Http\Controllers\DriverController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    // Finance
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/transactions', [\App\Http\Controllers\FinanceController::class, 'transactions'])->name('finance.transactions');
    Route::get('/finance/invoices', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.invoices');
    Route::post('/finance/payments/{rental}', [\App\Http\Controllers\FinanceController::class, 'storePayment'])->name('finance.payments.store');

    // Vendors & AP Engine
    Route::post('vendors/{vendor}/suspend', [\App\Http\Controllers\Portal\VendorController::class, 'suspend'])->name('vendors.suspend');
    Route::resource('vendors', \App\Http\Controllers\Portal\VendorController::class);
    Route::resource('expenses', \App\Http\Controllers\Portal\ExpenseController::class);
    Route::post('contracts/{contract}/activate', [\App\Http\Controllers\Portal\ContractController::class, 'activate'])->name('contracts.activate');
    Route::post('contracts/{contract}/terminate', [\App\Http\Controllers\Portal\ContractController::class, 'terminate'])->name('contracts.terminate');
    Route::get('contracts/{contract}/download', [\App\Http\Controllers\Portal\ContractController::class, 'downloadContract'])->name('contracts.download');
    Route::resource('contracts', \App\Http\Controllers\Portal\ContractController::class);
    // Fines Module
    Route::get('fines/import', [\App\Http\Controllers\Portal\FineController::class, 'import'])->name('fines.import');
    Route::post('fines/import', [\App\Http\Controllers\Portal\FineController::class, 'storeImport'])->name('fines.storeImport');
    Route::get('fines/report', [\App\Http\Controllers\Portal\FineController::class, 'report'])->name('fines.report');
    // Tracking Module
    Route::get('tracking', function() {
        return view('portal.tracking.map');
    })->name('tracking.map');

    Route::resource('fines', \App\Http\Controllers\Portal\FineController::class);

    // Invoices Module
    Route::resource('invoices', \App\Http\Controllers\Portal\InvoiceController::class);
    Route::get('invoices/{invoice}/download', [\App\Http\Controllers\Portal\InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    Route::post('invoices/{invoice}/send', [\App\Http\Controllers\Portal\InvoiceController::class, 'sendEmail'])->name('invoices.send');
    Route::post('invoices/{invoice}/payments', [\App\Http\Controllers\Portal\InvoicePaymentController::class, 'store'])->name('invoices.payments.store');

    Route::post('vendor-bills/{vendor_bill}/approve', [\App\Http\Controllers\Portal\VendorBillController::class, 'approve'])->name('vendor-bills.approve');
    Route::post('vendor-bills/{vendor_bill}/pay', [\App\Http\Controllers\Portal\VendorBillController::class, 'recordPayment'])->name('vendor-bills.pay');
    Route::post('vendor-bills/{vendor_bill}/cancel', [\App\Http\Controllers\Portal\VendorBillController::class, 'cancel'])->name('vendor-bills.cancel');
    Route::resource('vendor-bills', \App\Http\Controllers\Portal\VendorBillController::class);

    // Reports
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::post('exports', [\App\Http\Controllers\Portal\ExportController::class, 'store'])->name('exports.store');

    // Export Endpoints (Excel + PDF)
    Route::get('/reports/export/invoices/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'invoicesExcel'])->name('reports.export.invoices.excel');
    Route::get('/reports/export/invoices/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'invoicesPdf'])->name('reports.export.invoices.pdf');

    Route::get('/reports/export/rentals/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'rentalsExcel'])->name('reports.export.rentals.excel');
    Route::get('/reports/export/rentals/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'rentalsPdf'])->name('reports.export.rentals.pdf');

    Route::get('/reports/export/drivers/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'driversExcel'])->name('reports.export.drivers.excel');
    Route::get('/reports/export/drivers/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'driversPdf'])->name('reports.export.drivers.pdf');

    Route::middleware(['role:Accountant|Owner'])->group(function () {
        Route::get('/reports/export/balance-sheet/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'balanceSheetExcel'])->name('reports.export.balance-sheet.excel');
        Route::get('/reports/export/balance-sheet/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'balanceSheetPdf'])->name('reports.export.balance-sheet.pdf');

        Route::get('/reports/export/profit-loss/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'profitLossExcel'])->name('reports.export.profit-loss.excel');
        Route::get('/reports/export/profit-loss/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'profitLossPdf'])->name('reports.export.profit-loss.pdf');

        Route::get('/reports/export/cash-flow/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'cashFlowExcel'])->name('reports.export.cash-flow.excel');
        Route::get('/reports/export/cash-flow/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'cashFlowPdf'])->name('reports.export.cash-flow.pdf');

        Route::get('/reports/export/trial-balance/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'trialBalanceExcel'])->name('reports.export.trial-balance.excel');
        Route::get('/reports/export/trial-balance/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'trialBalancePdf'])->name('reports.export.trial-balance.pdf');

        Route::get('/reports/export/general-ledger/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'generalLedgerExcel'])->name('reports.export.general-ledger.excel');
        Route::get('/reports/export/general-ledger/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'generalLedgerPdf'])->name('reports.export.general-ledger.pdf');
    });

    Route::get('/reports/export/vendor-bills/excel', [App\Http\Controllers\Portal\ReportExportController::class, 'vendorBillsExcel'])->name('reports.export.vendor-bills.excel');
    Route::get('/reports/export/vendor-bills/pdf', [App\Http\Controllers\Portal\ReportExportController::class, 'vendorBillsPdf'])->name('reports.export.vendor-bills.pdf');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity.index');

    // Accounting Core Engine (ERP)
    Route::middleware(['role:Accountant|Owner'])->prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\AccountingController::class, 'index'])->name('index');
        Route::get('/coa', [App\Http\Controllers\Portal\AccountingController::class, 'coa'])->name('coa');
        Route::get('/journals', [App\Http\Controllers\Portal\AccountingController::class, 'journals'])->name('journals');
        Route::get('/reports', [App\Http\Controllers\Portal\AccountingController::class, 'reports'])->name('reports.index');
        Route::get('/reports/profit-loss', [App\Http\Controllers\Portal\AccountingController::class, 'profitLoss'])->name('reports.pnl');
        Route::get('/reports/balance-sheet', [App\Http\Controllers\Portal\AccountingController::class, 'balanceSheet'])->name('reports.balance-sheet');
        Route::get('/reports/trial-balance', [App\Http\Controllers\Portal\AccountingController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('/reports/general-ledger', [App\Http\Controllers\Portal\AccountingController::class, 'generalLedger'])->name('reports.general-ledger');
        Route::get('/reports/cash-flow', [App\Http\Controllers\Portal\AccountingController::class, 'cashFlow'])->name('reports.cash-flow');

        // Payroll
        Route::get('/payroll', [App\Http\Controllers\Portal\AccountingController::class, 'payrollIndex'])->name('payroll.index');
        Route::get('/payroll/create', [App\Http\Controllers\Portal\AccountingController::class, 'payrollCreate'])->name('payroll.create');
        Route::post('/payroll', [App\Http\Controllers\Portal\AccountingController::class, 'payrollStore'])->name('payroll.store');
        Route::get('/payroll/{id}', [App\Http\Controllers\Portal\AccountingController::class, 'payrollShow'])->name('payroll.show');
        Route::post('/payroll/{id}/post', [App\Http\Controllers\Portal\AccountingController::class, 'payrollPost'])->name('payroll.post');
        Route::get('/payroll/slip/{id}', [App\Http\Controllers\Portal\AccountingController::class, 'salarySlip'])->name('payroll.slip');
    });

    // Command Center Snapshots
    Route::get('/command-center/api', [App\Http\Controllers\Portal\CommandCenterController::class, 'apiSnapshot'])->name('command-center.api');

    // Settings & Roles
    Route::get('/settings', [App\Http\Controllers\Portal\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Portal\SettingsController::class, 'update'])->name('settings.update');
    
    // Tenant Workspace Configuration Overrides
    Route::get('/system-preferences', [App\Http\Controllers\Portal\SettingController::class, 'index'])->name('system-preferences.index');
    Route::post('/system-preferences', [App\Http\Controllers\Portal\SettingController::class, 'update'])->name('system-preferences.update');
    Route::get('/settings/roles', [App\Http\Controllers\Portal\RoleManagementController::class, 'index'])->name('settings.roles.index');
    Route::post('/settings/roles/invite', [App\Http\Controllers\Portal\RoleManagementController::class, 'invite'])->name('settings.roles.invite');
    Route::put('/settings/roles/{userToUpdate}', [App\Http\Controllers\Portal\RoleManagementController::class, 'update'])->name('settings.roles.update');
});

// ============================================================
// DRIVER MOBILE PORTAL — Phase 5
// ============================================================
Route::prefix('driver')->name('driver.')->group(function () {

    // Public: Login / Logout (no auth needed)
    Route::get('/login', [App\Http\Controllers\Driver\DriverSessionController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Driver\DriverSessionController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Driver\DriverSessionController::class, 'logout'])->name('logout');

    // Protected: Requires driver PIN session
    Route::middleware('driver.pin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Driver\DriverDashboardController::class, 'index'])->name('dashboard');

        // Trip Management
        Route::get('/trip/{rental}', [App\Http\Controllers\Driver\DriverTripController::class, 'show'])->name('trip.show');
        Route::post('/trip/{rental}/start', [App\Http\Controllers\Driver\DriverTripController::class, 'start'])->name('trip.start');
        Route::post('/trip/{rental}/complete', [App\Http\Controllers\Driver\DriverTripController::class, 'complete'])->name('trip.complete');

        // Fuel Upload
        Route::get('/fuel/create', [App\Http\Controllers\Driver\DriverReportController::class, 'createFuel'])->name('fuel.create');
        Route::post('/fuel', [App\Http\Controllers\Driver\DriverReportController::class, 'storeFuel'])->name('fuel.store');

        // Breakdown Report
        Route::get('/breakdown/create', [App\Http\Controllers\Driver\DriverReportController::class, 'createBreakdown'])->name('breakdown.create');
        Route::post('/breakdown', [App\Http\Controllers\Driver\DriverReportController::class, 'storeBreakdown'])->name('breakdown.store');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Driver\DriverReportController::class, 'profile'])->name('profile');
    });
});
