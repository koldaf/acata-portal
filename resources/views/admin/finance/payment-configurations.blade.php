@extends('layouts.app')

@section('title', 'Payment Configurations - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payment Configurations</h1>
            <p class="text-muted mb-0">Define reusable payment items by code, amount, and description.</p>
        </div>
        <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-primary">Back to Finance</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3">
            <h2 class="h5 mb-0">Create Payment Item</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.finance.payment-configurations.store') }}" class="row g-3">
                @csrf
                <div class="col-md-3"><label class="form-label">Code</label><input class="form-control" name="code" value="{{ old('code') }}" placeholder="MEMBERSHIP_2026" required></div>
                <div class="col-md-3"><label class="form-label">Title</label><input class="form-control" name="title" value="{{ old('title') }}" placeholder="Annual Membership" required></div>
                <div class="col-md-2"><label class="form-label">Amount</label><input type="number" step="0.01" min="0.01" class="form-control" name="amount" value="{{ old('amount') }}" required></div>
                <div class="col-md-2"><label class="form-label">Currency</label><input class="form-control" name="currency" value="{{ old('currency', 'NGN') }}" required></div>
                <div class="col-md-2 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><label class="form-check-label">Active</label></div></div>
                <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea></div>
                <div class="col-12"><button class="btn btn-primary" type="submit">Create</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead><tr><th>Code</th><th>Title</th><th>Amount</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($paymentConfigurations as $config)
                        <tr>
                            <td><code>{{ $config->code }}</code></td>
                            <td>{{ $config->title }}</td>
                            <td>{{ number_format((float) $config->amount, 2) }} {{ $config->currency }}</td>
                            <td>{{ $config->description ?: '-' }}</td>
                            <td>{{ $config->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.finance.payment-configurations.edit', $config) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.finance.payment-configurations.update', $config) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="title" value="{{ $config->title }}">
                                        <input type="hidden" name="description" value="{{ $config->description }}">
                                        <input type="hidden" name="amount" value="{{ $config->amount }}">
                                        <input type="hidden" name="currency" value="{{ $config->currency }}">
                                        @if($config->is_active)
                                            <input type="hidden" name="is_active" value="0">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Deactivate</button>
                                        @else
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-success">Activate</button>
                                        @endif
                                    </form>
                                    <form method="POST" action="{{ route('admin.finance.payment-configurations.destroy', $config) }}" onsubmit="return confirm('Delete this payment config?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No payment configurations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">{{ $paymentConfigurations->links() }}</div>
    </div>
</div>
@endsection
