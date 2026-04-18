@extends('layouts.company')

@section('title', 'Create Maintenance Record')

@section('header_title')
<div class="flex items-center space-x-2">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight uppercase">New Maintenance Record</h1>
</div>
@endsection

@section('content')

<div class="py-12" x-data="maintenanceForm()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('company.maintenance.store') }}" class="space-y-6">
            @csrf

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">1. Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Maintenance Type <span class="text-red-500">*</span></label>
                            <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="preventive">Preventive</option>
                                <option value="corrective">Corrective</option>
                                <option value="accident">Accident</option>
                                <option value="inspection">Inspection</option>
                                <option value="insurance">Insurance</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Initial Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" x-model="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="scheduled">Scheduled</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed instantly</option>
                            </select>
                        </div>

                        <div x-show="status === 'scheduled'">
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                            <input type="date" id="scheduled_date" name="scheduled_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div x-show="status === 'completed'">
                            <label for="completed_date" class="block text-sm font-medium text-gray-700">Completed Date</label>
                            <input type="date" id="completed_date" name="completed_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">2. Vehicle & Vendor</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehicle <span class="text-red-500">*</span></label>
                            <select id="vehicle_id" name="vehicle_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select a Vehicle...</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->name }} ({{ $vehicle->vehicle_number }}) - Odo: {{ $vehicle->current_odometer }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="odometer_reading" class="block text-sm font-medium text-gray-700">Current Odometer Reading</label>
                            <input type="number" id="odometer_reading" name="odometer_reading" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. 150000">
                            <p class="text-xs text-gray-500 mt-1">If empty, system will use current vehicle value.</p>
                        </div>

                        <div>
                            <label for="vendor_id" class="block text-sm font-medium text-gray-700">Vendor / Workshop</label>
                            <select id="vendor_id" name="vendor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Internal / Unknown</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->first_name }} {{ $vendor->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Cost Breakdown (Dynamic) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-medium text-gray-900">3. Cost Breakdown</h3>
                        <button type="button" @click="addItem" class="px-3 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded text-sm hover:bg-gray-200">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div class="flex-shrink-0 w-32">
                                    <select x-model="item.item_type" :name="`items[${index}][item_type]`" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="part">Spare Part</option>
                                        <option value="labor">Labor</option>
                                        <option value="service">Service</option>
                                    </select>
                                </div>
                                <div class="flex-grow">
                                    <input type="text" x-model="item.description" :name="`items[${index}][description]`" placeholder="Description" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="w-24">
                                    <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" step="0.01" min="0" placeholder="Qty" required @input="calculateTotal" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="w-32">
                                    <input type="number" x-model="item.unit_cost" :name="`items[${index}][unit_cost]`" step="0.01" min="0" placeholder="Unit Cost" required @input="calculateTotal" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="w-32 text-right">
                                    <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.quantity * item.unit_cost)"></span>
                                </div>
                                <div>
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times-circle text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="items.length === 0" class="text-center py-6 text-gray-500 bg-gray-50 rounded border border-dashed border-gray-300">
                            No cost components added. Click "Add Item" to detail the costs.
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div class="mt-6 border-t pt-4 flex justify-end">
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Estimated Cost</span>
                            <div class="text-3xl font-bold text-gray-900" x-text="formatCurrency(grandTotal)"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('company.maintenance.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md shadow hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 font-medium">Save Maintenance Record</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('maintenanceForm', () => ({
            status: 'scheduled',
            items: [],
            grandTotal: 0,

            addItem() {
                this.items.push({
                    item_type: 'part',
                    description: '',
                    quantity: 1,
                    unit_cost: 0
                });
                this.calculateTotal();
            },

            removeItem(index) {
                this.items.splice(index, 1);
                this.calculateTotal();
            },

            calculateTotal() {
                this.grandTotal = this.items.reduce((sum, item) => {
                    return sum + ((item.quantity || 0) * (item.unit_cost || 0));
                }, 0);
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('en-AE', {
                    style: 'currency',
                    currency: 'AED'
                }).format(value);
            }
        }));
    });
</script>
@endpush
@endsection