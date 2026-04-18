<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Subscription & Usage</h5>
    </div>
    <div class="card-body">
        @if($subscription)
        <div class="row">
            <!-- Plan Info -->
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Current Plan</h6>
                <h4>{{ $subscription->plan->name }}</h4>
                <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'trialing' ? 'info' : 'warning') }}">
                    {{ ucfirst($subscription->status) }}
                </span>

                @if($subscription->trial_ends_at && $subscription->status === 'trialing')
                <p class="mt-2 mb-0 text-muted small">
                    <i class="bi bi-clock"></i> Trial ends {{ $subscription->trial_ends_at->diffForHumans() }}
                </p>
                @endif

                @if($subscription->current_period_end)
                <p class="mt-1 mb-0 text-muted small">
                    Renews {{ $subscription->current_period_end->format('M d, Y') }}
                </p>
                @endif
            </div>

            <!-- Usage Stats -->
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Resource Usage</h6>

                @foreach(['vehicles', 'users', 'branches'] as $resource)
                @php
                $usage = $quotaStatus[$resource];
                $percentage = $usage['percentage'];
                $color = $percentage > 80 ? 'danger' : ($percentage > 60 ? 'warning' : 'success');
                @endphp

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-capitalize">{{ $resource }}</span>
                        <span class="text-muted small">{{ $usage['current'] }} / {{ $usage['limit'] ?? '∞' }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-{{ $color }}" role="progressbar"
                            style="width: {{ min($percentage, 100) }}%"
                            aria-valuenow="{{ $percentage }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('subscription.show') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-credit-card"></i> Manage Subscription
            </a>
            <a href="{{ route('subscription.upgrade') }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-arrow-up-circle"></i> Upgrade Plan
            </a>
        </div>
        @else
        <div class="alert alert-warning">
            <h6 class="alert-heading">No Active Subscription</h6>
            <p class="mb-0">Please contact support to activate your subscription.</p>
        </div>
        @endif
    </div>
</div>