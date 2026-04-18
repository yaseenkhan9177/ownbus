@extends('layouts.app')

@section('content')
<div class="container py-4" x-data="rentalQuote()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Rental Quote</h1>
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <!-- Input Form -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('rentals.store') }}" method="POST" id="quoteForm">
                        @csrf

                        <h5 class="card-title mb-3">Rental Details</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->company_name ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rental Type</label>
                                <select name="rental_type" class="form-select" x-model="form.rental_type" @change="calculatePrice()">
                                    <option value="daily">Daily</option>
                                    <option value="hourly">Hourly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="distance">Distance Based</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date & Time</label>
                                <input type="datetime-local" name="start_date" class="form-control" x-model="form.start_date" @change="calculatePrice()" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date & Time</label>
                                <input type="datetime-local" name="end_date" class="form-control" x-model="form.end_date" @change="calculatePrice()" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pickup Location</label>
                                <input type="text" name="pickup_location" class="form-control" placeholder="e.g. Dubai Airport" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dropoff Location</label>
                                <input type="text" name="dropoff_location" class="form-control" placeholder="e.g. Hotel ABC">
                            </div>
                        </div>

                        <!-- Manual Override (Optional) -->
                        <!-- <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="manualOverride">
                            <label class="form-check-label" for="manualOverride">Manual Price Override</label>
                        </div> -->

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Create Quote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Price Preview -->
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Estimated Price</h5>
                </div>
                <div class="card-body">
                    <div x-show="loading" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2">Calculating...</p>
                    </div>

                    <div x-show="!loading" class="price-breakdown">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Base Rent:</span>
                            <span class="fw-bold" x-text="formatMoney(price.base_rent)"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger" x-show="price.extra_hours_charge > 0">
                            <span>Extra Hours:</span>
                            <span x-text="formatMoney(price.extra_hours_charge)"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger" x-show="price.extra_km_charge > 0">
                            <span>Extra KM:</span>
                            <span x-text="formatMoney(price.extra_km_charge)"></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span class="fw-bold" x-text="formatMoney(price.subtotal)"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>VAT (5%):</span>
                            <span x-text="formatMoney(price.vat)"></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Total:</h4>
                            <h4 class="text-primary mb-0" x-text="formatMoney(price.total)"></h4>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted" x-text="policyName"></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rentalQuote', () => ({
            loading: false,
            form: {
                rental_type: 'daily',
                start_date: '',
                end_date: ''
            },
            price: {
                base_rent: 0,
                extra_hours_charge: 0,
                extra_km_charge: 0,
                subtotal: 0,
                vat: 0,
                total: 0
            },
            policyName: 'No policy selected',

            init() {
                // Set default dates?
            },

            async calculatePrice() {
                if (!this.form.start_date || !this.form.end_date) return;

                this.loading = true;

                try {
                    const response = await fetch('{{ route("api.rental.price") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.price = data;
                        this.policyName = data.line_items?.policy_name || 'Standard Rate';
                    }
                } catch (error) {
                    console.error('Price calculation failed:', error);
                } finally {
                    this.loading = false;
                }
            },

            formatMoney(value) {
                return 'AED ' + Number(value).toFixed(2);
            }
        }));
    });
</script>
@endsection