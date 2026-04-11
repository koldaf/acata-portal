@extends('layouts.app')

@section('title', 'Finance Dashboard - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Finance Dashboard</h1>
            <p class="text-muted mb-0">Track association revenue, payment status, and transaction history.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.payment-configurations.index') }}" class="btn btn-outline-primary">Payment Configs</a>
            <a href="{{ route('admin.finance.activity-logs.index') }}" class="btn btn-outline-primary">Activity Logs</a>
            <a href="{{ route('admin.finance.export', request()->query()) }}" class="btn btn-primary">Export CSV</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">Back to Admin Dashboard</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Total Revenue</p><h2 class="h4 mb-0">{{ number_format((float) $summary['total_revenue'], 2) }}</h2></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Successful Payments</p><h2 class="h4 mb-0">{{ $summary['total_successful'] }}</h2></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Pending</p><h2 class="h4 mb-0">{{ $summary['pending_payments'] }}</h2></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Failed / Abandoned</p><h2 class="h4 mb-0">{{ $summary['failed_payments'] }}</h2></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Membership Revenue</p><h2 class="h4 mb-0">{{ number_format((float) $summary['membership_revenue'], 2) }}</h2></div></div></div>
        <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-muted mb-1">Event Revenue</p><h2 class="h4 mb-0">{{ number_format((float) $summary['event_revenue'], 2) }}</h2></div></div></div>
    </div>

    <form method="GET" action="{{ route('admin.finance.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by reference or member">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach(['pending', 'success', 'failed', 'abandoned', 'refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="payment_type" class="form-select">
                <option value="">All types</option>
                @foreach(['membership', 'event', 'resource', 'other'] as $paymentType)
                    <option value="{{ $paymentType }}" @selected(request('payment_type') === $paymentType)>{{ ucfirst($paymentType) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="From">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="To">
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Member</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Gateway</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td><code>{{ $payment->reference }}</code></td>
                            <td>{{ $payment->member?->display_name ?? 'Unmatched' }}</td>
                            <td>{{ ucfirst($payment->payment_type) }}</td>
                            <td>{{ ucfirst($payment->status) }}</td>
                            <td>{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td>{{ ucfirst($payment->gateway) }}</td>
                            <td>{{ $payment->paid_at?->format('M j, Y g:i A') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
