@extends('layouts.app')

@section('title', 'My Interests - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">My Interests</h2>
                    <p class="text-muted mb-0">View your interests, remove unwanted ones, and add new areas of focus.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
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

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-tags me-2"></i>
                        Registered Interests ({{ $memberInterests->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($memberInterests->isEmpty())
                        <p class="text-muted mb-0">You have not added any interests yet.</p>
                    @else
                        <div class="row g-2">
                            @foreach($memberInterests as $interest)
                                <div class="col-md-6 col-xl-4">
                                    <div class="d-flex align-items-center justify-content-between border rounded p-2 h-100">
                                        <span class="me-2">{{ $interest->interest }}</span>
                                        <form method="POST" action="{{ route('member.interests.remove', $interest) }}" onsubmit="return confirm('Remove this interest?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove interest">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Interest
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('member.interests.add') }}" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label for="interest_id" class="form-label">Choose Existing Interest</label>
                            <select id="interest_id" name="interest_id" class="form-select">
                                <option value="">Select an interest</option>
                                @foreach($availableInterests as $interest)
                                    <option value="{{ $interest->id }}" @selected(old('interest_id') == $interest->id)>
                                        {{ $interest->interest }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pick from active interests not yet on your profile.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="interest_name" class="form-label">Or Create New Interest</label>
                            <input id="interest_name" name="interest_name" type="text" class="form-control" value="{{ old('interest_name') }}" placeholder="Example: Psychometric Modeling">
                            <small class="text-muted">Use this when your interest is not in the list.</small>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>
                                Add Interest
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
