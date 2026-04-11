@extends('layouts.app')

@section('title', 'Event Registrants - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Registrants: {{ $event->title }}</h1>
            <p class="text-muted mb-0">Track registrations and attendance status for this event.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">Back to Events</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-2"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Total</p><h2 class="h5 mb-0">{{ $counts['total'] }}</h2></div></div></div>
        <div class="col-md-2"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Registered</p><h2 class="h5 mb-0">{{ $counts['registered'] }}</h2></div></div></div>
        <div class="col-md-2"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Attended</p><h2 class="h5 mb-0">{{ $counts['attended'] }}</h2></div></div></div>
        <div class="col-md-2"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Cancelled</p><h2 class="h5 mb-0">{{ $counts['cancelled'] }}</h2></div></div></div>
        <div class="col-md-2"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">No Show</p><h2 class="h5 mb-0">{{ $counts['no_show'] }}</h2></div></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrants as $registration)
                        <tr>
                            <td>{{ $registration->member?->display_name ?? 'Unknown Member' }}</td>
                            <td>{{ $registration->member?->email ?? '-' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $registration->status)) }}</td>
                            <td>{{ $registration->registered_at?->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No registrants yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $registrants->links() }}
        </div>
    </div>
</div>
@endsection
