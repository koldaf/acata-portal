@extends('layouts.app')

@section('title', 'Resources - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">Resource Library</h2>
                    <p class="text-muted mb-0">Download association resources and reference materials.</p>
                </div>
                @if($user->isAdmin())
                    <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-primary">Manage Resources</a>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Resource</th>
                                <th>Visibility</th>
                                <th>Size</th>
                                <th>Uploaded</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resources as $resource)
                                <tr>
                                    <td>
                                        <strong>{{ $resource->title }}</strong>
                                        @if($resource->description)
                                            <div class="text-muted small">{{ $resource->description }}</div>
                                        @endif
                                        <div class="small text-muted">{{ $resource->file_name }}</div>
                                    </td>
                                    <td>{{ ucfirst($resource->visibility) }}</td>
                                    <td>{{ $resource->size_kb ? $resource->size_kb . ' KB' : '-' }}</td>
                                    <td>{{ $resource->created_at?->format('M j, Y') }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.resources.download', $resource) }}" class="btn btn-sm btn-primary">Download</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">No resources available yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-body border-top">{{ $resources->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
