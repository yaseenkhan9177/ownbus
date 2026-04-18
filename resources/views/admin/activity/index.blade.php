@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Audit Log</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td><small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small></td>
                        <td>{{ $log->user->name ?? 'System' }}</td>
                        <td>
                            <span class="badge {{ $log->event == 'deleted' ? 'bg-danger' : ($log->event == 'created' ? 'bg-success' : 'bg-primary') }}">
                                {{ ucfirst($log->event) }}
                            </span>
                        </td>
                        <td>
                            @if($log->subject_type)
                            <small>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</small>
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $log->description }}</td>
                        <td><small>{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">No activity found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection