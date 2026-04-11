@extends('layouts.app')

@section('title', 'Certificate Settings - ACATA Portal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Certificate Settings</h1>
            <p class="text-muted mb-0">Configure signatory details and signatures for each certificate type.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">Back to Admin Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        @foreach($types as $type)
            @php $setting = $settings->get($type); @endphp
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h2 class="h5 mb-0">{{ ucfirst($type) }} Certificate</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.certificate-settings.update', $type) }}" enctype="multipart/form-data" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label" for="signatory_name_{{ $type }}">Signatory Name</label>
                                <input id="signatory_name_{{ $type }}" name="signatory_name" type="text" class="form-control" value="{{ old('signatory_name', $setting?->signatory_name) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="signatory_title_{{ $type }}">Signatory Title</label>
                                <input id="signatory_title_{{ $type }}" name="signatory_title" type="text" class="form-control" value="{{ old('signatory_title', $setting?->signatory_title) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="signature_{{ $type }}">Signature Image</label>
                                <input id="signature_{{ $type }}" name="signature" type="file" class="form-control" accept="image/png,image/jpeg">
                            </div>
                            @if($setting?->signature_path)
                                <div class="col-12">
                                    <p class="small text-muted mb-2">Current Signature</p>
                                    <img src="{{ route('admin.certificate-settings.signature', $setting) }}" alt="{{ ucfirst($type) }} signature" style="max-width: 220px; max-height: 90px; object-fit: contain;">
                                </div>
                            @endif
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save {{ ucfirst($type) }} Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
