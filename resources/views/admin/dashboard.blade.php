@extends('layouts.app')

@section('title', 'Admin Dashboard - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Admin Dashboard</h1>
            <p class="text-muted mb-0">Manage members and platform operations.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.members.index') }}" class="btn btn-primary">
                <i class="bi bi-people me-1"></i>Member Directory
            </a>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-event me-1"></i>Manage Events
            </a>
            <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-folder2-open me-1"></i>Manage Resources
            </a>
            <a href="{{ route('admin.certificate-settings.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-pen me-1"></i>Certificate Settings
            </a>
            <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-cash-coin me-1"></i>Finance
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Members</p>
                    <h2 class="h4 mb-0">{{ $memberCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Admins</p>
                    <h2 class="h4 mb-0">{{ $adminCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Super Admins</p>
                    <h2 class="h4 mb-0">{{ $superAdminCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-1">Active Members</p>
                    <h2 class="h4 mb-0">{{ $activeMemberCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h2 class="h5">Roadmap Progress</h2>
            <p class="text-muted mb-0">Completed: roles, admin delegation, member directory, events CRUD, resources, certificate settings, and finance ledger/webhook integration.</p>
        </div>
    </div>
</div>
@endsection
