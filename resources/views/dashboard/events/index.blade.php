@extends('layouts.app')

@section('title', 'Events - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">Upcoming Events</h2>
                    <p class="text-muted mb-0">Browse published events and open an event to view details and register.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($events->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                        <h3 class="h5 mb-2">No upcoming events right now</h3>
                        <p class="text-muted mb-0">Check back later for new ACATA event announcements.</p>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach($events as $event)
                        @php
                            $isEnded = $event->ends_at && $event->ends_at->isPast();
                            $isLive = $event->starts_at->isPast() && !$isEnded;
                            $isUpcoming = $event->starts_at->isFuture();
                        @endphp
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h3 class="h5 mb-0">{{ $event->title }}</h3>
                                        @if($isLive)
                                            <span class="badge bg-success">Live</span>
                                        @elseif($isUpcoming)
                                            <span class="badge bg-primary">Upcoming</span>
                                        @else
                                            <span class="badge bg-secondary">Ended</span>
                                        @endif
                                    </div>

                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $event->starts_at->format('M j, Y g:i A') }}
                                        @if($event->ends_at)
                                            - {{ $event->ends_at->format('M j, Y g:i A') }}
                                        @endif
                                    </p>

                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        {{ $event->location ?: 'Location to be announced' }}
                                    </p>

                                    <p class="mb-3">{{ \Illuminate\Support\Str::limit($event->description, 140) ?: 'No event description provided yet.' }}</p>

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <small class="text-muted">
                                            Registrations: {{ $event->registrations_count }}
                                            @if($event->capacity)
                                                / {{ $event->capacity }}
                                            @endif
                                        </small>
                                        <a href="{{ route('dashboard.events.show', $event) }}" class="btn btn-sm btn-primary">View Event</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
