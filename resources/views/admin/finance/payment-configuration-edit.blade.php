@extends('layouts.app')

@section('title', 'Edit Payment Configuration - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Payment Configuration</h1>
            <p class="text-muted mb-0">Update amount, description, status, and currency for this payment item.</p>
        </div>
        <a href="{{ route('admin.finance.payment-configurations.index') }}" class="btn btn-outline-primary">Back to Payment Configurations</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.finance.payment-configurations.update', $paymentConfiguration) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label class="form-label" for="code">Code</label>
                    <input id="code" type="text" class="form-control" value="{{ $paymentConfiguration->code }}" readonly>
                    <div class="form-text">Code is immutable to keep payment references stable.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="title">Title</label>
                    <input id="title" name="title" type="text" class="form-control" value="{{ old('title', $paymentConfiguration->title) }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label" for="amount">Amount</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" class="form-control" value="{{ old('amount', $paymentConfiguration->amount) }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label" for="currency">Currency</label>
                    <input id="currency" name="currency" type="text" class="form-control" value="{{ old('currency', $paymentConfiguration->currency) }}" maxlength="10" required>
                </div>

                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $paymentConfiguration->description) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $paymentConfiguration->is_active))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a href="{{ route('admin.finance.payment-configurations.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
