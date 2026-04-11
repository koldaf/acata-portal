@extends('layouts.app')

@section('title', 'Payment Catalog - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">Payment Catalog</h2>
                    <p class="text-muted mb-0">Select what you want to pay for using configured payment codes.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-warning">
                <strong>Annual Membership Dues Policy:</strong> All members must pay annual dues before accessing events, certificates, resources, and member directory features. ACATA financial year runs from August 1 to July 31. A grace period is available through August 31 each year.
            </div>

            <div class="row g-3">
                @forelse($paymentConfigurations as $config)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h3 class="h5 mb-1">{{ $config->title }}</h3>
                                <p class="text-muted small mb-2">Code: <code>{{ $config->code }}</code></p>
                                <p class="mb-3">{{ $config->description ?: 'No description available.' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ number_format((float) $config->amount, 2) }} {{ $config->currency }}</strong>
                                    <form method="POST" action="{{ route('payments.start', $config->code) }}">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" type="submit">Pay Now</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-info">No active payment items are available.</div></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
