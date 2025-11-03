@extends('layouts.app')

@section('title', 'Join ACATA - Register')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 4rem; height: 4rem;">
                            <i class="bi bi-person-plus fs-2"></i>
                        </div>
                        <h2 class="fw-bold">Join ACATA</h2>
                        <p class="text-muted">Create your account and become part of Africa's CAT community</p>
                    </div>

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">Personal Information</h5>
                                <!-- Titile -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <select class="form-select @error('title') is-invalid @enderror" 
                                            id="title" name="title">
                                        <option value="">Select your title</option>
                                        <option value="Mr." {{ old('title') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                        <option value="Ms." {{ old('title') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                        <option value="Mrs." {{ old('title') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                        <option value="Dr." {{ old('title') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                        <option value="Prof." {{ old('title') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                    </select>
                                    @error('title')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <!-- First Name -->
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                           placeholder="Enter your first name" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <!-- Middle Name -->
                                <div class="mb-3">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                           id="middle_name" name="middle_name" value="{{ old('middle_name') }}" 
                                           placeholder="Enter your middle name">
                                    @error('middle_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <!-- Last Name -->
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                           placeholder="Enter your last name" required>
                                    @error('last_name')
                                        <div class="invalid-feedback"></div>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" 
                                           placeholder="+1234567890">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">Account Information</h5>

                                 <!-- Membership Type -->
                                <div class="mb-3">
                                    <label for="membership_type" class="form-label">Membership Type</label>
                                    <select class="form-select @error('membership_type') is-invalid @enderror" 
                                            id="membership_type" name="membership_type">
                                        <option value="">Select your membership type</option>
                                        @foreach ($memtypes as $types)
                                            <option value="{{ $types->membership_type }}" {{ old('membership_type') == $types->membership_type ? 'selected' : '' }}>{{ $types->membership_type }}</option>
                                        @endforeach
                                    </select>
                                    @error('membership_type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                               
                                <!-- Email Address -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           placeholder="Enter your email" required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Create a password" required>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        Must be at least 8 characters long.
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Confirm your password" required>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">Professional Information</h5>
                                
                                <div class="row">
                                    <!-- Organization -->
                                    <div class="col-md-6 mb-3">
                                        <label for="organization" class="form-label">Organization</label>
                                        <input type="text" class="form-control @error('organization') is-invalid @enderror" 
                                               id="organization" name="organization" value="{{ old('organization') }}" 
                                               placeholder="Your university or company">
                                        @error('organization')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Job Title -->
                                    <div class="col-md-6 mb-3">
                                        <label for="job_title" class="form-label">Job Title</label>
                                        <input type="text" class="form-control @error('job_title') is-invalid @enderror" 
                                               id="job_title" name="job_title" value="{{ old('job_title') }}" 
                                               placeholder="Your professional title">
                                        @error('job_title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Country -->
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-select @error('country') is-invalid @enderror" 
                                            id="country" name="country">
                                        <option value="">Select your country</option>
                                        <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                                        <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                        <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>Kenya</option>
                                        <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                                        <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                                        <!-- Add more African countries -->
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Areas of Interest -->
                                <div class="mb-4">
                                    <label class="form-label">Areas of Interest (Select all that apply)</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            @foreach ($interests as $interest)
                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="interests[]" value="{{ $interest->id }}" id="{{ $interest->interest }}">
                                                <label class="form-check-label" for="{{ $interest->interest }}">
                                                    {{ $interest->interest }}
                                                </label>
                                            </div>
                                            @endforeach
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" name="terms" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> 
                                    and <a href="#" class="text-decoration-none">Privacy Policy</a> *
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-check me-2"></i>Create Account
                            </button>
                        </div>
                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted">Already have an account? 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Sign in here</a>
                        </p>
                    </div>

                    <!-- Benefits -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="fw-semibold mb-2">Benefits of Joining ACATA:</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li><i class="bi bi-check-circle text-success me-2"></i>Access to exclusive CAT resources</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Professional networking opportunities</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Membership certificate</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Event discounts and early registration</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection