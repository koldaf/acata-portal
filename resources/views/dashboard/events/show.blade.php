@extends('layouts.app')

@section('title', $event->title . ' - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">{{ $event->title }}</h2>
                    <p class="text-muted mb-0">Event details, timeline, and registration.</p>
                </div>
                <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-primary">Back to Events</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($hasStarted && !$isEnded)
                                    <span class="badge bg-success">Live</span>
                                @elseif(!$hasStarted)
                                    <span class="badge bg-primary">Upcoming</span>
                                @else
                                    <span class="badge bg-secondary">Ended</span>
                                @endif

                                @if($event->registration_open)
                                    <span class="badge bg-info text-dark">Registration Open</span>
                                @else
                                    <span class="badge bg-dark">Registration Closed</span>
                                @endif

                                @if($isFull)
                                    <span class="badge bg-warning text-dark">Event is Full</span>
                                @endif
                            </div>

                            <p class="mb-3">{{ $event->description ?: 'No event description has been added yet.' }}</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h3 class="h6 mb-2">Start Time</h3>
                                        <p class="mb-0">{{ $event->starts_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h3 class="h6 mb-2">End Time</h3>
                                        <p class="mb-0">{{ $event->ends_at ? $event->ends_at->format('M j, Y g:i A') : 'Not specified' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h3 class="h6 mb-2">Location</h3>
                                        <p class="mb-0">{{ $event->location ?: 'To be announced' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h3 class="h6 mb-2">Capacity</h3>
                                        <p class="mb-0">
                                            {{ $registrationsCount }}
                                            @if($event->capacity)
                                                / {{ $event->capacity }}
                                            @else
                                                registered
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body text-center">
                            <h3 class="h6 mb-3">Event Countdown</h3>
                            <p id="countdown-label" class="text-muted mb-2">Loading countdown...</p>
                            <div id="countdown" class="fw-bold fs-5"></div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="h6 mb-3">Registration</h3>

                            @if($isRegistered)
                                <div class="alert alert-success mb-0">You are already registered for this event.</div>
                            @elseif(!$event->registration_open)
                                <div class="alert alert-secondary mb-0">Registration is currently closed for this event.</div>
                            @elseif($isEnded)
                                <div class="alert alert-secondary mb-0">This event has ended.</div>
                            @elseif($isFull)
                                <div class="alert alert-warning mb-0">Event is full.</div>
                            @elseif($registrationAllowed)
                                <form method="POST" action="{{ route('dashboard.events.register', $event) }}" class="d-grid gap-3">
                                    @csrf
                                    <div>
                                        <label class="form-label" for="notes">Notes (optional)</label>
                                        <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="Add any relevant info for event organizers">{{ old('notes') }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Register for Event</button>
                                </form>
                            @else
                                <div class="alert alert-secondary mb-0">Registration is not available for this event.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const countdownEl = document.getElementById('countdown');
    const labelEl = document.getElementById('countdown-label');

    if (!countdownEl || !labelEl) {
        return;
    }

    const startAt = new Date(@json($event->starts_at->toIso8601String())).getTime();
    const endAt = @json(optional($event->ends_at)->toIso8601String());
    const endAtMs = endAt ? new Date(endAt).getTime() : null;

    const updateCountdown = () => {
        const now = Date.now();

        if (endAtMs && now >= endAtMs) {
            labelEl.textContent = 'Event ended';
            countdownEl.textContent = '00d 00h 00m 00s';
            return;
        }

        if (now >= startAt) {
            labelEl.textContent = 'Event started';
            countdownEl.textContent = '00d 00h 00m 00s';
            return;
        }

        const distance = startAt - now;
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        labelEl.textContent = 'Time until event starts';
        countdownEl.textContent = `${String(days).padStart(2, '0')}d ${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
    };

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();
</script>
@endpush
