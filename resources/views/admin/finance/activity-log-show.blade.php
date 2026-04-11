@extends('layouts.app')

@section('title', 'Activity Log Details - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Activity Log Details</h1>
            <p class="text-muted mb-0">Inspect request metadata and payload for this audit record.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.activity-logs.export', request()->query()) }}" class="btn btn-primary">Export Filtered CSV</a>
            <a href="{{ route('admin.finance.activity-logs.index') }}" class="btn btn-outline-primary">Back to Activity Logs</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h2 class="h5 mb-0">Request Summary</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Performed At</dt>
                        <dd class="col-sm-8">{{ $activityLog->performed_at?->format('M j, Y g:i:s A') ?? '-' }}</dd>

                        <dt class="col-sm-4">Action</dt>
                        <dd class="col-sm-8">{{ $activityLog->action }}</dd>

                        <dt class="col-sm-4">Method</dt>
                        <dd class="col-sm-8">{{ $activityLog->http_method }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ $activityLog->response_status ?? '-' }}</dd>

                        <dt class="col-sm-4">Route</dt>
                        <dd class="col-sm-8">{{ $activityLog->route_name ?: '-' }}</dd>

                        <dt class="col-sm-4">Path</dt>
                        <dd class="col-sm-8">{{ $activityLog->path }}</dd>

                        <dt class="col-sm-4">IP Address</dt>
                        <dd class="col-sm-8">{{ $activityLog->ip_address ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h2 class="h5 mb-0">Actor</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $activityLog->member?->display_name ?? 'Unknown' }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $activityLog->member?->email ?? '-' }}</dd>

                        <dt class="col-sm-4">User Agent</dt>
                        <dd class="col-sm-8 text-break">{{ $activityLog->user_agent ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h2 class="h5 mb-0">Payload</h2>
                </div>
                <div class="card-body">
                    @if($formattedPayload)
                        <pre class="mb-0 bg-light p-3 rounded border" style="max-height: 540px; overflow: auto;">{{ $formattedPayload }}</pre>
                    @else
                        <p class="text-muted mb-0">No payload was captured for this request.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
