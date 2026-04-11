@extends('layouts.app')

@section('title', 'Edit Event - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Event</h1>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">Back to Events</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.events.update', $event) }}" class="row g-3">
                @csrf
                @method('PUT')
                @include('admin.events.partials.form-fields', ['event' => $event])
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Event</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
