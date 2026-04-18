@extends('portal.layout')

@section('title', 'Complete Payment')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Complete Your Payment</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Payment Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Details</h2>

                <!-- Stripe Elements will be inserted here -->
                <form id="payment-form">
                    @csrf

                    <div id="payment-element" class="mb-6">
                        <!-- Stripe Payment Element will mount here -->
                    </div>

                    <div id="payment-message" class="hidden mb-4 p-4 rounded-md"></div>

                    <button
                        id="submit-button"
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-md font-semibold">
                        <span id="button-text">Pay AED {{ number_format($rental->final_amount, 2) }}</span>
                        <div id="spinner" class="hidden ml-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </form>

                <!-- Secure Payment Badge -->
                <div class="mt-6 flex items-center justify-center text-sm text-gray-600">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Secure payment powered by Stripe
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="font-semibold text-gray-900 mb-4">Order Summary</h3>

                <div class="space-y-3 mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $rental->vehicle->name }}</p>
                        <p class="text-xs text-gray-600">{{ $rental->vehicle->model }}</p>
                    </div>

                    <div class="text-sm">
                        <p class="text-gray-600">Pickup: {{ $rental->start_date->format('M d, Y') }}</p>
                        <p class="text-gray-600">Return: {{ $rental->end_date->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Base Price</span>
                        <span class="font-semibold">AED {{ number_format($rental->rate_amount, 2) }}</span>
                    </div>
                    @if($rental->driver_fee > 0)
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Driver Fee</span>
                        <span class="font-semibold">AED {{ number_format($rental->driver_fee, 2) }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-blue-600">AED {{ number_format($rental->final_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const clientSecret = '{{ $paymentIntent->client_secret }}';

    const options = {
        clientSecret: clientSecret,
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#2563eb',
            }
        }
    };

    const elements = stripe.elements(options);
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const paymentMessage = document.getElementById('payment-message');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        setLoading(true);

        const {
            error
        } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: '{{ route("portal.payments.success", $rental) }}',
            },
        });

        if (error) {
            showMessage(error.message, 'error');
            setLoading(false);
        }
    });

    function setLoading(loading) {
        if (loading) {
            submitButton.disabled = true;
            spinner.classList.remove('hidden');
            buttonText.textContent = 'Processing...';
        } else {
            submitButton.disabled = false;
            spinner.classList.add('hidden');
            buttonText.textContent = 'Pay AED {{ number_format($rental->final_amount, 2) }}';
        }
    }

    function showMessage(message, type = 'error') {
        paymentMessage.classList.remove('hidden');
        paymentMessage.textContent = message;

        if (type === 'error') {
            paymentMessage.classList.add('bg-red-100', 'text-red-700', 'border', 'border-red-400');
            paymentMessage.classList.remove('bg-green-100', 'text-green-700', 'border-green-400');
        } else {
            paymentMessage.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-400');
            paymentMessage.classList.remove('bg-red-100', 'text-red-700', 'border-red-400');
        }
    }
</script>
@endpush
@endsection