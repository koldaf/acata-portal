@extends('layouts.app')

@section('title', 'My Certificates - ACATA Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            @include('dashboard.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">My Certificates</h2>
                    <p class="text-muted mb-0">Download and manage your ACATA certificates</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#certificateHelpModal">
                        <i class="bi bi-question-circle me-1"></i>Help
                    </button>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Certificate Cards -->
            <div class="row g-4">
                <!-- Membership Certificate -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-award text-warning me-2"></i>
                                        Membership Certificate
                                    </h5>
                                    <p class="text-muted mb-0">Official ACATA membership certification</p>
                                </div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Available
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small text-muted mb-1">Certificate ID</label>
                                            <p class="fw-semibold mb-0">ACATA-MEM-{{ auth()->user()->member_id }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small text-muted mb-1">Issue Date</label>
                                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse(auth()->user()->created_on)->format('F j, Y') }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small text-muted mb-1">Member Since</label>
                                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse(auth()->user()->created_on)->format('F Y') }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small text-muted mb-1">Status</label>
                                            <p class="fw-semibold mb-0">
                                                <span class="badge bg-{{ auth()->user()->isActive() ? 'success' : 'warning' }}">
                                                    {{ ucfirst(auth()->user()->status) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="d-grid gap-2">
                                        <form action="{{ route('member.certificates.download') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="certificate_type" value="membership">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="bi bi-download me-2"></i>Download PDF
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#previewCertificateModal">
                                            <i class="bi bi-eye me-2"></i>Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Certificates Section -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar-event text-info me-2"></i>
                                Event Certificates
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($eventCertificates && $eventCertificates->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Event Name</th>
                                                <th>Date</th>
                                                <th>Certificate ID</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($eventCertificates as $certificate)
                                                <tr>
                                                    <td>
                                                        <h6 class="mb-1">{{ $certificate->event_name }}</h6>
                                                        <small class="text-muted">{{ $certificate->event_type }}</small>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($certificate->event_date)->format('M j, Y') }}</td>
                                                    <td>
                                                        <code class="small">{{ $certificate->certificate_id }}</code>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $certificate->status === 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($certificate->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <form action="{{ route('member.certificates.download') }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="certificate_type" value="event">
                                                                <input type="hidden" name="event_id" value="{{ $certificate->id }}">
                                                                <button type="submit" class="btn btn-outline-primary">
                                                                    <i class="bi bi-download"></i>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-outline-secondary" 
                                                                    data-bs-toggle="tooltip" 
                                                                    title="Verify Certificate"
                                                                    onclick="verifyCertificate('{{ $certificate->certificate_id }}')">
                                                                <i class="bi bi-shield-check"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x text-muted fs-1 mb-3"></i>
                                    <h5 class="text-muted">No Event Certificates Yet</h5>
                                    <p class="text-muted mb-4">You haven't attended any events that provide certificates.</p>
                                    <a href="{{ route('events') }}" class="btn btn-primary">
                                        <i class="bi bi-calendar-event me-2"></i>Browse Events
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Certificate Verification -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-check text-success me-2"></i>
                                Certificate Verification
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <p class="mb-3">Verify the authenticity of any ACATA certificate using the unique certificate ID.</p>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="verifyCertificateId" 
                                               placeholder="Enter certificate ID (e.g., ACATA-MEM-{{ auth()->user()->member_id }})">
                                        <button class="btn btn-success" type="button" onclick="verifyCertificate()">
                                            <i class="bi bi-search me-2"></i>Verify
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="bg-light rounded p-4">
                                        <i class="bi bi-qr-code fs-1 text-muted mb-2"></i>
                                        <p class="small text-muted mb-0">Scan QR code on certificate to verify</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download History -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Download History
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($downloadHistory && $downloadHistory->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Certificate</th>
                                                <th>Downloaded On</th>
                                                <th>IP Address</th>
                                                <th>Certificate ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($downloadHistory as $history)
                                                <tr>
                                                    <td>
                                                        <span class="fw-semibold">{{ $history->certificate_type }}</span>
                                                        @if($history->event_name)
                                                            <br><small class="text-muted">{{ $history->event_name }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($history->downloaded_at)->format('M j, Y g:i A') }}</td>
                                                    <td><code class="small">{{ $history->ip_address }}</code></td>
                                                    <td><code class="small">{{ $history->certificate_id }}</code></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-download text-muted fs-1 mb-3"></i>
                                    <p class="text-muted mb-0">No certificate downloads yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Certificate Modal -->
<div class="modal fade" id="previewCertificateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Certificate Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="certificate-preview border rounded p-4 text-center">
                    <div class="certificate-header mb-4">
                        <h2 class="text-warning fw-bold">ACATA</h2>
                        <p class="text-muted mb-0">Association for Computer Adaptive Testing in Africa</p>
                    </div>
                    
                    <div class="certificate-body my-5">
                        <h4 class="text-uppercase text-muted mb-3">Certificate of Membership</h4>
                        <p class="mb-4">This is to certify that</p>
                        <h3 class="fw-bold text-primary mb-3">{{ auth()->user()->full_name }}</h3>
                        <p class="mb-4">is a recognized member in good standing of the</p>
                        <p class="fw-semibold mb-4">Association for Computer Adaptive Testing in Africa</p>
                    </div>
                    
                    <div class="certificate-footer mt-5">
                        <div class="row">
                            <div class="col-6 text-start">
                                <div class="border-top pt-3">
                                    <p class="mb-1 fw-semibold">Date Issued</p>
                                    <p class="text-muted mb-0">{{ \Carbon\Carbon::parse(auth()->user()->created_on)->format('F j, Y') }}</p>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="border-top pt-3">
                                    <p class="mb-1 fw-semibold">Certificate ID</p>
                                    <p class="text-muted mb-0">ACATA-MEM-{{ auth()->user()->member_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form action="{{ route('member.certificates.download') }}" method="POST" class="me-auto">
                    @csrf
                    <input type="hidden" name="certificate_type" value="membership">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Download PDF
                    </button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="certificateHelpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-question-circle text-primary me-2"></i>
                    Certificate Help
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>About Your Certificates</h6>
                <ul class="mb-3">
                    <li>Membership certificates are available to all active members</li>
                    <li>Event certificates are generated after event completion</li>
                    <li>All certificates include unique verification IDs</li>
                </ul>
                
                <h6>Download Instructions</h6>
                <ul class="mb-3">
                    <li>Click "Download PDF" to get your certificate</li>
                    <li>Certificates are generated in high-resolution PDF format</li>
                    <li>You can download certificates multiple times</li>
                </ul>
                
                <h6>Verification</h6>
                <ul>
                    <li>Use the verification section to validate any certificate</li>
                    <li>Certificates include QR codes for easy verification</li>
                    <li>Employers can verify your certificates at any time</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.certificate-preview {
    background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%), 
                linear-gradient(-45deg, #f8f9fa 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #f8f9fa 75%), 
                linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
</style>

<script>
function verifyCertificate(certificateId = null) {
    const certId = certificateId || document.getElementById('verifyCertificateId').value;
    
    if (!certId) {
        alert('Please enter a certificate ID');
        return;
    }
    
    // Open verification in new tab
    const verifyUrl = "{{ url('/verify-certificate') }}/" + encodeURIComponent(certId);
    window.open(verifyUrl, '_blank');
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection