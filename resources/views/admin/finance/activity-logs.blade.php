@extends('layouts.app')

@section('title', 'Activity Logs - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Activity Logs</h1>
            <p class="text-muted mb-0">Operational trail of user actions across the application.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.finance.activity-logs.export', request()->query()) }}" class="btn btn-primary">Export CSV</a>
            <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-primary">Back to Finance</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.finance.activity-logs.index') }}" class="row g-2 mb-3">
        <div class="col-md-4"><input class="form-control" name="member" value="{{ request('member') }}" placeholder="Search member"></div>
        <div class="col-md-2">
            <select class="form-select" name="method">
                <option value="">All methods</option>
                @foreach(['GET','POST','PUT','PATCH','DELETE'] as $method)
                    <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4"><input class="form-control" name="route_name" value="{{ request('route_name') }}" placeholder="Route name"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit">Filter</button></div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead><tr><th>When</th><th>User</th><th>Action</th><th>Route</th><th>Method</th><th>Status</th><th>Path</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->performed_at?->format('M j, Y g:i:s A') }}</td>
                            <td>{{ $log->member?->display_name ?? 'Unknown' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->route_name ?: '-' }}</td>
                            <td>{{ $log->http_method }}</td>
                            <td>{{ $log->response_status }}</td>
                            <td>{{ $log->path }}</td>
                            <td>
                                <a href="{{ route('admin.finance.activity-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No activity logs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
