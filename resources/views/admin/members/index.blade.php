@extends('layouts.app')

@section('title', 'Admin Members - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Member Directory</h1>
            <p class="text-muted mb-0">Search members and manage admin roles.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">Back to Admin Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.members.index') }}" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, email, or member ID">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">All roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
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
                        <th>Member</th>
                        <th>Email</th>
                        <th>Member ID</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Role Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $member->display_name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->member_id }}</td>
                            <td>
                                <span class="badge {{ $member->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $member->role ?? 'member')) }}</td>
                            <td>
                                @if($currentUser->isSuperAdmin())
                                    <form method="POST" action="{{ route('admin.members.role.update', $member) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-select form-select-sm">
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}" @selected(($member->role ?? 'member') === $role)>
                                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                    </form>
                                @else
                                    <span class="text-muted">Super admin only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body border-top">
            {{ $members->links() }}
        </div>
    </div>
</div>
@endsection
