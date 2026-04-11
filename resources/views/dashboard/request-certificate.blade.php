@extends('layouts.app')

@section('title', 'My Certificates - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">My Certificates</h2>
                    <p class="text-muted mb-0">Download and manage your ACATA certificates</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#certificateHelpModal">
                        <i class="bi bi-question-circle me-1"></i>Help
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
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

            <!-- Certificate Cards -->
            <div class="row g-4">
                <!-- Membership Certificate -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="card-title mb-1 fw-bold">
                                    <i class="bi bi-award text-warning me-2"></i>
                                    {{ $member_type['membership_type'] }} Certificate ({{ $member_type['cost'] }})
                                </h2>
                                <div class="d-flex gap-2 align-items-center">
                                    <form method="POST" action="{{ route('payments.start', 'MEMBERSHIP') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Pay Membership Fee</button>
                                    </form>
                                    <a href="{{ route('dashboard.payments') }}" class="btn btn-outline-primary btn-sm">Payment Catalog</a>
                                    @if(!empty($member_type['url']))
                                        <a href="{{ $member_type['url'] }}" title="Open hosted payment page" target="_blank" class="btn btn-outline-primary btn-sm">Hosted Checkout</a>
                                    @endif
                                </div>
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <p class="mb-0 text-muted">Use the integrated payment flow to generate a tracked transaction reference and redirect to the gateway. Hosted checkout remains available as a fallback.</p>
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
</div>

@endsection