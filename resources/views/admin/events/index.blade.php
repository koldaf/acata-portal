@extends('layouts.app')

@section('title', 'Admin Events - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Events</h1>
            <p class="text-muted mb-0">Create and manage events, then monitor registrant numbers.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Event
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Starts</th>
                        <th>Location</th>
                        <th>Registrants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                <strong>{{ $event->title }}</strong>
                                @if($event->capacity)
                                    <div class="text-muted small">Capacity: {{ $event->capacity }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $event->status === 'published' ? 'bg-success' : ($event->status === 'closed' ? 'bg-dark' : 'bg-secondary') }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td>{{ $event->starts_at?->format('M j, Y g:i A') }}</td>
                            <td>{{ $event->location ?: '-' }}</td>
                            <td>
                                <a href="{{ route('admin.events.registrants', $event) }}" class="text-decoration-none">
                                    {{ $event->registrations_count }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No events created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection
