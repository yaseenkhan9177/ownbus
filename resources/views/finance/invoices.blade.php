@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Finance & Invoices</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice Ref</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $rental)
                        @php
                        // Calculate paid amount from transactions
                        // Simplification: we rely on rental->payment_status principally,
                        // but could sum up actual transactions here for display.
                        $paidAmount = $rental->transactions->where('description', 'like', 'Payment Received%')->sum(function($t) {
                        // This is tricky without amount column on transaction.
                        // We'd need to load journal entries.
                        // For MVP display, let's just show status.
                        return 0; // Placeholder
                        });
                        @endphp
                        <tr>
                            <td class="fw-bold">
                                <a href="{{ route('rentals.show', $rental) }}">#{{ $rental->contract_number }}</a>
                            </td>
                            <td>
                                {{ $rental->customer->name }}
                                <div class="small text-muted">{{ $rental->customer->company_name }}</div>
                            </td>
                            <td>{{ $rental->end_datetime->format('d M Y') }}</td> <!-- Invoice Date usually end date -->
                            <td>{{ number_format($rental->final_amount, 2) }}</td>
                            <td>
                                <!-- Placeholder until we have robust sum -->
                                <span class="text-muted">-</span>
                            </td>
                            <td>
                                @php
                                $badges = [
                                'pending_payment' => 'warning',
                                'partially_paid' => 'info',
                                'paid' => 'success',
                                'overdue' => 'danger',
                                ];
                                $badge = $badges[$rental->payment_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $rental->payment_status)) }}</span>
                            </td>
                            <td>
                                @if($rental->payment_status !== 'paid')
                                <button class="btn btn-sm btn-success"
                                    onclick="openPaymentModal('{{ $rental->id }}', '{{ $rental->contract_number }}', '{{ $rental->final_amount }}')">
                                    Record Payment
                                </button>
                                @else
                                <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No pending invoices found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="paymentForm" method="POST" action="">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Recording payment for Invoice <strong id="modalContractNumber"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" id="modalAmount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference / Receipt #</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(id, contractNumber, total) {
        document.getElementById('modalContractNumber').innerText = '#' + contractNumber;
        document.getElementById('modalAmount').value = total; // Default to full amount

        // Update form action
        const form = document.getElementById('paymentForm');
        form.action = `/finance/payments/${id}`; // Adjust route as needed

        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }
</script>
@endsection