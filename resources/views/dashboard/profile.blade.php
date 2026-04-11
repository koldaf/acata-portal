@extends('layouts.app')

@section('title', 'My Profile - ACATA Portal')

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
                        <h2 class="h3 fw-bold mb-1">My Profile</h2>
                        <p class="text-muted mb-0">Manage your personal information and settings</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#profileHelpModal">
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

                <!-- Profile Cards -->
                <div class="row g-4">
                    <!-- Membership Certificate -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h2 class="card-title mb-1 fw-bold">
                                        <i class="bi bi-person-circle text-primary me-2"></i>
                                        My Profile
                                    </h2>
                                    <h5 class="text-muted mb-0">
                                    </h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="card col-md-10 mb-10 mb-md-0 border-0 shadow-sm">
                                        <div class="row"> 
                                            <div class="col-4">
                                                <img src="{{ auth()->user()->display_profile_picture }}" class="img-fluid rounded-circle" alt="{{ $user->last_name }}">
                                            </div>
                                            <div class="card-body col-8">
                                                <h5 class="card-title">{{ $user->name }}</h5>
                                                <p class="card-text">{{ $user->bio }}</p>
                                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#profileUpdateModal">Edit Bio</a>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <label for="surname" class="form-label">Surname</label>
                                            <input type="text" class="form-control" id="surname"
                                                value="{{ $user->last_name }}" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="middle_name" class="form-label">Middle Name</label>
                                            <input type="text" class="form-control" id="middle_name"
                                                value="{{ $user->middle_name }}" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name"
                                                value="{{ $user->first_name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-4">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" value="{{ $user->email }}"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="membership_type" class="form-label">Membership Type</label>
                                            <input type="text" class="form-control" id="membership_type"
                                                value="{{ $user->membership_type }}" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" value="{{ $user->phone }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-4">
                                            <label for="organization" class="form-label">Organization</label>
                                            <input type="text" class="form-control" id="organization"
                                                value="{{ $user->affiliation }}" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="position" class="form-label">Position</label>
                                            <input type="text" class="form-control" id="position"
                                                value="{{ $user->job_title }}" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control" id="country"
                                                value="{{ $user->country }}" readonly>
                                        </div>
                                    </div>

                                   

                                    
                                </div>

                                <!-- setup modal for uploading bio and profile picture -->
                                <div class="modal fade" id="profileUpdateModal" tabindex="-1"
                                    aria-labelledby="profileUpdateModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="profileUpdateModalLabel">Profile Update</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-3">Update your bio. Please provide accurate and up-to-date
                                                    information.</p>
                                                <textarea class="form-control" rows="5">
                                                        Please note that any changes to your profile information may take up to 48 hours to reflect on your account. 
                                                    </textarea>
                                                <button type="button" id="bioUpdate"
                                                    class="btn btn-success mt-3">Update</button>

                                                <hr>
                                                <h4 class="h5 fw-bold mb-3">Profile Picture</h4>
                                                <p class="mb-3">Update your profile picture. Please use a .jpeg or .png file
                                                    format only. Filesize cannot be more than 2MB.</p>
                                                <form id="profilePictureUpload" enctype="multipart/form-data">
                                                    <input type="file" class="form-control" id="profilePicture"
                                                        name="profilePicture">
                                                    <button type="button" id="profilePictureUploadBtn"
                                                        class="btn btn-success mt-3">Upload</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
@endsection
            @push('scripts')
                <script>
                    // JavaScript for handling profile update and picture upload can be added here
                    $(document).ready(function () {
                        $('#bioUpdate').click(function () {
                            var bioContent = $('#profileUpdateModal textarea').val();
                            //console.log('Bio content to update:', bioContent);

                            $.post("{{ route('member.profile.update') }}", {
                                bio: bioContent,
                                _token: "{{ csrf_token() }}"
                            }).done(function (response) {
                                console.dir(response);
                                alert('Bio updated successfully!');
                                $('#profileUpdateModal').modal('hide');
                            }).fail(function (xhr, status, error) {
                                console.log('Error updating bio: ' + error);
                            });
                            //

                            //alert('Bio update functionality is not implemented yet.');
                        });

                        $('#profilePictureUploadBtn').click(function () {

                            $.ajax({
                                url: "{{ route('member.profile.pix.update') }}",
                                type: 'POST',
                                data: new FormData(document.getElementById('profilePictureUpload')),
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    console.dir(response);
                                    alert('Profile picture uploaded successfully!');
                                    $('#profileUpdateModal').modal('hide');
                                },
                                error: function (xhr, status, error) {
                                    console.log('Error uploading profile picture: ' + error);
                                    alert('Failed to upload profile picture. Please try again.');
                                }
                            });
                        });
                    });

                </script>
            @endpush