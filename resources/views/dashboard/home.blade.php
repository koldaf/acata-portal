@extends('layouts.app')

@section('title', 'Member Dashboard - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <!-- Profile Picture -->
                    <div class="mb-3">
                        <img src="{{ auth()->user()->display_profile_picture }}" 
                             alt="Profile Picture" 
                             class="rounded-circle img-thumbnail" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    
                    <!-- Member Info -->
                    <h5 class="fw-bold mb-1">{{ auth()->user()->full_name }}</h5>
                    <p class="text-muted small mb-2">{{ auth()->user()->job_title ?: 'ACATA Member' }}</p>
                    
                    <!-- Member ID Badge -->
                    <div class="badge bg-primary mb-3">
                        <i class="bi bi-person-badge me-1"></i>
                        {{ auth()->user()->member_id }}
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="badge bg-{{ auth()->user()->isActive() ? 'success' : 'warning' }} mb-3">
                        <i class="bi bi-circle-fill me-1"></i>
                        {{ ucfirst(auth()->user()->status) }}
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="fw-bold mb-0">{{ auth()->user()->interests->count() }}</h6>
                                <small class="text-muted">Interests</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-bold mb-0">
                                {{ \Carbon\Carbon::parse(auth()->user()->created_on)->format('M Y') }}
                            </h6>
                            <small class="text-muted">Member Since</small>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                @include('dashboard.partials.sidebar')
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Welcome Alert -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">Welcome back, {{ auth()->user()->first_name }}!</h2>
                    <p class="text-muted mb-0">Here's your ACATA membership overview</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('member.profile') }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i>Edit Profile
                    </a>
                    <a href="{{ route('dashboard.certificates') }}" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Get Certificate
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Membership Status</h6>
                                    <h4 class="fw-bold text-{{ auth()->user()->isActive() ? 'success' : 'warning' }}">
                                        {{ ucfirst(auth()->user()->status) }}
                                    </h4>
                                </div>
                                <div class="bg-{{ auth()->user()->isActive() ? 'success' : 'warning' }} bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-shield-check text-{{ auth()->user()->isActive() ? 'success' : 'warning' }} fs-4"></i>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                {{ auth()->user()->isActive() ? 'Active membership' : 'Membership inactive' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Member Since</h6>
                                    <h4 class="fw-bold text-primary">
                                        {{ \Carbon\Carbon::parse(auth()->user()->created_on)->format('M Y') }}
                                    </h4>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-calendar-check text-primary fs-4"></i>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                {{ \Carbon\Carbon::parse(auth()->user()->created_on)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">My Interests</h6>
                                    <h4 class="fw-bold text-info">
                                        {{ auth()->user()->interests->count() }}
                                    </h4>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-tags text-info fs-4"></i>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                Professional areas of focus
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Email Status</h6>
                                    <h4 class="fw-bold text-{{ auth()->user()->isEmailVerified() ? 'success' : 'warning' }}">
                                        {{ auth()->user()->isEmailVerified() ? 'Verified' : 'Pending' }}
                                    </h4>
                                </div>
                                <div class="bg-{{ auth()->user()->isEmailVerified() ? 'success' : 'warning' }} bg-opacity-10 p-3 rounded">
                                    <i class="bi bi-{{ auth()->user()->isEmailVerified() ? 'envelope-check' : 'envelope-exclamation' }} text-{{ auth()->user()->isEmailVerified() ? 'success' : 'warning' }} fs-4"></i>
                                </div>
                            </div>
                            <p class="small text-muted mb-0">
                                {{ auth()->user()->isEmailVerified() ? 'Email verified' : 'Verify your email' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Quick Actions -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning-fill text-warning me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('member.profile') }}" class="card card-hover border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-person text-primary fs-5"></i>
                                            </div>
                                            <h6 class="fw-semibold mb-1">Update Profile</h6>
                                            <small class="text-muted">Edit your information</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('dashboard.certificates') }}" class="card card-hover border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-3">
                                            <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-award text-success fs-5"></i>
                                            </div>
                                            <h6 class="fw-semibold mb-1">Get Certificate</h6>
                                            <small class="text-muted">Download membership cert</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('events') }}" class="card card-hover border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-3">
                                            <div class="bg-info bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-calendar-event text-info fs-5"></i>
                                            </div>
                                            <h6 class="fw-semibold mb-1">Browse Events</h6>
                                            <small class="text-muted">View upcoming events</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('members.directory') }}" class="card card-hover border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-3">
                                            <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-people text-warning fs-5"></i>
                                            </div>
                                            <h6 class="fw-semibold mb-1">Member Network</h6>
                                            <small class="text-muted">Connect with members</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Interests -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-tags text-info me-2"></i>
                                My Interests
                            </h5>
                            <a href="{{ route('member.interests') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                        </div>
                        <div class="card-body">
                            @if(auth()->user()->interests->count() > 0)
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach(auth()->user()->interests->take(6) as $interest)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                            {{ $interest->interest }}
                                        </span>
                                    @endforeach
                                    @if(auth()->user()->interests->count() > 6)
                                        <span class="badge bg-secondary">
                                            +{{ auth()->user()->interests->count() - 6 }} more
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-tags text-muted fs-1 mb-3"></i>
                                    <p class="text-muted mb-3">You haven't added any interests yet.</p>
                                    <a href="{{ route('member.interests') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Add Interests
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Recent Activity
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-check-lg text-success"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Profile updated</h6>
                                            <p class="text-muted mb-0 small">You updated your professional information</p>
                                        </div>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                </div>
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-award text-info"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Certificate downloaded</h6>
                                            <p class="text-muted mb-0 small">You downloaded your membership certificate</p>
                                        </div>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </div>
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-calendar-event text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Event registration</h6>
                                            <p class="text-muted mb-0 small">You registered for "CAT Conference 2024"</p>
                                        </div>
                                        <small class="text-muted">3 days ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-hover {
    transition: all 0.2s ease-in-out;
}
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection