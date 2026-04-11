@extends('layouts.app')

@section('title', 'Admin Resources - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Resources</h1>
            <p class="text-muted mb-0">Upload and manage files available to members.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">Back to Admin Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3">
            <h2 class="h5 mb-0">Upload New Resource</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label" for="title">Title</label>
                    <input id="title" name="title" type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="visibility">Visibility</label>
                    <select id="visibility" name="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                        <option value="members" @selected(old('visibility') === 'members')>All Members</option>
                        <option value="admins" @selected(old('visibility') === 'admins')>Admins Only</option>
                    </select>
                    @error('visibility')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="resource_file">File</label>
                    <input id="resource_file" name="resource_file" type="file" class="form-control @error('resource_file') is-invalid @enderror" required>
                    @error('resource_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Upload Resource</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Visibility</th>
                        <th>Size</th>
                        <th>Uploaded By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td>
                                <strong>{{ $resource->title }}</strong>
                                <div class="text-muted small">{{ $resource->file_name }}</div>
                            </td>
                            <td>{{ ucfirst($resource->visibility) }}</td>
                            <td>{{ $resource->size_kb ? $resource->size_kb . ' KB' : '-' }}</td>
                            <td>{{ $resource->uploader?->display_name ?? 'System' }}</td>
                            <td>{{ $resource->created_at?->format('M j, Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('dashboard.resources.download', $resource) }}" class="btn btn-sm btn-outline-primary">Download</a>
                                    <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" onsubmit="return confirm('Delete this resource?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No resources uploaded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body border-top">
            {{ $resources->links() }}
        </div>
    </div>
</div>
@endsection
