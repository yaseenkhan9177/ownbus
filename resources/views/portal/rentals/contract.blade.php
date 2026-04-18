@extends('layouts.company')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4" x-data="contractWizard()" x-init="init()">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">New Rental Contract</h1>
            <p class="text-slate-400 mt-1">Deploy an enterprise-grade rental agreement with integrated accounting.</p>
        </div>
        <a href="{{ route('company.rentals.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white border border-slate-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('company.rentals.contract.store') }}" method="POST" class="space-y-8 pb-20">
        @csrf

        <!-- 1️⃣ Contract Basic Info -->
        <section class="bg-[#0f172a] rounded-3xl border border-slate-800 p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                </svg>
            </div>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center font-bold">1</div>
                <h2 class="text-xl font-bold text-white">Contract Basic Info</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Contract Number</label>
                    <input type="text" name="contract_no" value="{{ $contractNo }}" readonly class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-blue-400 font-mono font-bold focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Branch</label>
                    <select name="branch_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ Auth::user()->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Rental Type</label>
                    <select name="rental_type" x-model="form.rental_type" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="trip_based">Trip Based</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Start Date & Time</label>
                    <input type="datetime-local" name="start_date" x-model="form.start_date" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">End Date & Time</label>
                    <input type="datetime-local" name="end_date" x-model="form.end_date" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Status</label>
                    <select name="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="draft">Draft</option>
                        <option value="active" selected>Active</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- 2️⃣ Customer Details -->
            <section class="bg-[#0f172a] rounded-3xl border border-slate-800 p-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold">2</div>
                    <h2 class="text-xl font-bold text-white">Customer Details</h2>
                </div>

                <div class="space-y-6" x-data="{ showModal: false }">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-semibold text-slate-400">Select Existing Customer</label>
                            <button type="button" @click="showModal = true" class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center gap-1 group">
                                <svg class="w-3 h-3 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                                Add New Customer
                            </button>
                        </div>
                        <select name="customer_id" x-model="form.customer_id" @change="updateCustomerDetails()" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-cnic="{{ $customer->cnic_no }}" data-phone="{{ $customer->phone }}" data-address="{{ $customer->address }}">
                                {{ $customer->name }} ({{ $customer->company_name ?? 'Individual' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Modal for Quick Customer Add -->
                    <template x-if="showModal">
                        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                            <div @click="showModal = false" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
                            <div class="bg-[#0f172a] border border-slate-800 w-full max-w-lg rounded-3xl p-8 shadow-2xl relative">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-white tracking-tight">Quick Add Customer</h3>
                                    <button @click="showModal = false" class="text-slate-500 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg></button>
                                </div>
                                <div class="space-y-4">
                                    <input type="text" id="new_customer_name" placeholder="Full Name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:ring-2 focus:ring-blue-500">
                                    <input type="text" id="new_customer_phone" placeholder="Phone Number" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:ring-2 focus:ring-blue-500">
                                    <input type="text" id="new_customer_cnic" placeholder="CNIC / ID Number" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:ring-2 focus:ring-blue-500">
                                    <textarea id="new_customer_address" placeholder="Residential Address" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:ring-2 focus:ring-blue-500 h-24"></textarea>
                                </div>
                                <div class="mt-8 flex gap-3">
                                    <button @click="showModal = false" type="button" class="flex-1 py-3 rounded-xl border border-slate-800 text-slate-400 font-bold hover:bg-slate-800 transition">Cancel</button>
                                    <button @click="quickAddCustomer()" type="button" class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-900/20 hover:bg-blue-500 transition">Save Customer</button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 gap-4 bg-slate-900/40 p-5 rounded-2xl border border-slate-800/50">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">CNIC / ID:</span>
                            <span class="text-slate-300 font-medium" x-text="customer.cnic || '—'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Phone:</span>
                            <span class="text-slate-300 font-medium" x-text="customer.phone || '—'"></span>
                        </div>
                        <div class="text-sm">
                            <span class="text-slate-500">Address:</span>
                            <p class="text-slate-300 mt-1 leading-relaxed" x-text="customer.address || '—'"></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3️⃣ Vehicle & Driver Assignment -->
            <section class="bg-[#0f172a] rounded-3xl border border-slate-800 p-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600/20 text-emerald-400 flex items-center justify-center font-bold">3</div>
                    <h2 class="text-xl font-bold text-white">Vehicle & Driver Assignment</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Select Vehicle (Available Only)</label>
                        <select name="vehicle_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} - {{ $vehicle->model }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] text-slate-500 uppercase font-bold tracking-wider">❌ Already rented vehicles are hidden</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Assign Driver</label>
                        <select name="driver_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="">No Driver (Self Drive)</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] text-slate-500 uppercase font-bold tracking-wider">❌ Busy drivers are hidden</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- 4️⃣ Pricing Section -->
            <section class="bg-[#0f172a] rounded-3xl border border-slate-800 p-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-amber-600/20 text-amber-400 flex items-center justify-center font-bold">4</div>
                    <h2 class="text-xl font-bold text-white">Pricing Section</h2>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Base Rent (AED)</label>
                        <input type="number" name="base_rent" x-model.number="form.base_rent" @input="calculateTotals()" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Extra KM Rate</label>
                        <input type="number" name="extra_km_rate" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Fuel Included?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="fuel_included" value="1" class="w-4 h-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700">
                                <span class="text-sm text-slate-300">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="fuel_included" value="0" checked class="w-4 h-4 text-amber-500 focus:ring-amber-500 bg-slate-900 border-slate-700">
                                <span class="text-sm text-slate-300">No</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Security Deposit</label>
                        <input type="number" name="security_deposit" x-model.number="form.security_deposit" @input="calculateTotals()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Tax %</label>
                        <input type="number" name="tax_percent" x-model.number="form.tax_percent" @input="calculateTotals()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Discount (AED)</label>
                        <input type="number" name="discount" x-model.number="form.discount" @input="calculateTotals()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>

                <div class="mt-8 bg-slate-900 p-6 rounded-2xl border border-slate-800 space-y-3">
                    <div class="flex justify-between text-sm text-slate-400">
                        <span>Subtotal (Base - Discount):</span>
                        <span x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-400">
                        <span>VAT Amount:</span>
                        <span x-text="formatCurrency(taxAmount)"></span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-slate-800">
                        <span class="text-lg font-bold text-white">Grand Total:</span>
                        <span class="text-lg font-bold text-blue-400" x-text="formatCurrency(grandTotal)"></span>
                        <input type="hidden" name="total_amount" :value="grandTotal">
                        <input type="hidden" name="tax_amount" :value="taxAmount">
                    </div>
                </div>
            </section>

            <!-- 5️⃣ Payment Section -->
            <section class="bg-[#0f172a] rounded-3xl border border-slate-800 p-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-600/20 text-purple-400 flex items-center justify-center font-bold">5</div>
                    <h2 class="text-xl font-bold text-white">Payment Section</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Payment Method</label>
                        <select name="payment_method" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500 outline-none">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank Transfer / Card</option>
                            <option value="Partial">Partial Payment</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-2">Amount Paid (AED)</label>
                            <input type="number" name="paid_amount" x-model.number="form.paid_amount" @input="calculateTotals()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-400 mb-2">Due Date</label>
                            <input type="date" name="due_date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl border-2 border-dashed border-slate-800 bg-slate-900/40">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Remaining Balance</h4>
                                <p class="text-2xl font-black mt-1" :class="remaining > 0 ? 'text-red-400' : 'text-emerald-400'" x-text="formatCurrency(remaining)"></p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest" :class="remaining > 0 ? 'bg-red-500/10 text-red-500' : 'bg-emerald-500/10 text-emerald-500'">
                                    <span x-text="remaining > 0 ? 'Due' : 'Settled'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="fixed bottom-0 left-64 right-0 bg-[#0f172a]/80 backdrop-blur-xl border-t border-slate-800 p-6 flex justify-between items-center z-50">
            <div class="flex gap-4 items-center">
                <div class="text-sm">
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Contract To Be Deployed</p>
                    <p class="text-white font-bold" x-text="'{{ $contractNo }}'"></p>
                </div>
                <div class="h-8 w-px bg-slate-800 mx-2"></div>
                <div>
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Grand Total</p>
                    <p class="text-blue-400 font-black text-lg" x-text="formatCurrency(grandTotal)"></p>
                </div>
            </div>

            <button type="submit" class="group flex items-center gap-3 bg-blue-600 hover:bg-blue-500 text-white px-10 py-4 rounded-2xl font-bold shadow-lg shadow-blue-900/40 transition-all hover:scale-105 active:scale-95">
                <span>Deploy Contract</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7m0 0l-7 7m7-7H6" />
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    function contractWizard() {
        return {
            form: {
                rental_type: 'daily',
                start_date: '{{ now()->format("Y-m-d\TH:i") }}',
                end_date: '{{ now()->addDays(1)->format("Y-m-d\TH:i") }}',
                customer_id: '',
                base_rent: 0,
                security_deposit: 0,
                tax_percent: 5,
                discount: 0,
                paid_amount: 0
            },
            customer: {
                cnic: '',
                phone: '',
                address: ''
            },
            subtotal: 0,
            taxAmount: 0,
            grandTotal: 0,
            remaining: 0,

            init() {
                this.calculateTotals();
            },

            async quickAddCustomer() {
                const name = document.getElementById('new_customer_name').value;
                const phone = document.getElementById('new_customer_phone').value;
                const cnic = document.getElementById('new_customer_cnic').value;
                const address = document.getElementById('new_customer_address').value;

                if (!name || !phone) {
                    alert('Name and Phone are required.');
                    return;
                }

                try {
                    const response = await fetch('{{ route("company.customers.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name,
                            phone,
                            cnic_no: cnic,
                            address
                        })
                    });

                    if (response.ok) {
                        const customer = await response.json();
                        // Add to list and select
                        const select = document.querySelector('select[name="customer_id"]');
                        const option = new Option(`${customer.name} (New)`, customer.id);
                        option.dataset.cnic = customer.cnic_no || '';
                        option.dataset.phone = customer.phone || '';
                        option.dataset.address = customer.address || '';
                        select.add(option);
                        this.form.customer_id = customer.id;
                        this.updateCustomerDetails();
                        this.showModal = false;
                    } else {
                        const error = await response.json();
                        alert('Error adding customer: ' + (error.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Connection error.');
                }
            },

            updateCustomerDetails() {
                const select = document.querySelector('select[name="customer_id"]');
                const option = select.options[select.selectedIndex];
                if (option.value) {
                    this.customer.cnic = option.dataset.cnic;
                    this.customer.phone = option.dataset.phone;
                    this.customer.address = option.dataset.address;
                } else {
                    this.customer.cnic = '';
                    this.customer.phone = '';
                    this.customer.address = '';
                }
            },

            calculateTotals() {
                // Formula provided by user:
                // Subtotal = Base Rent - Discount
                // Tax = Subtotal × Tax%
                // Total = Subtotal + Tax
                // Grand Total = Total + Security Deposit

                const base = parseFloat(this.form.base_rent) || 0;
                const disc = parseFloat(this.form.discount) || 0;
                const taxP = parseFloat(this.form.tax_percent) || 0;
                const deposit = parseFloat(this.form.security_deposit) || 0;
                const paid = parseFloat(this.form.paid_amount) || 0;

                this.subtotal = base - disc;
                this.taxAmount = this.subtotal * (taxP / 100);
                const total = this.subtotal + this.taxAmount;
                this.grandTotal = total + deposit;
                this.remaining = this.grandTotal - paid;
            },

            formatCurrency(value) {
                return 'AED ' + Number(value).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }
    }
</script>

<style>
    /* Custom Scrollbar for better UX */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #020617;
    }

    ::-webkit-scrollbar-thumb {
        background: #1e293b;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #334155;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
@endsection