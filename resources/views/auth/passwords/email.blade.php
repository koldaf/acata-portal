@extends('layouts.app')

@section('title', 'Login - ACATA Portal')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 4rem; height: 4rem;">
                                <i class="bi bi-key fs-2"></i>
                            </div>
                            <h2 class="fw-bold">Retrieve Password</h2>
                            <p class="text-muted">Enter your email to retrieve your password</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif


                        <!-- Login Form -->
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="Enter your email" required
                                    autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                           
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="text-decoration-none">
                                        I now remember my password?
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Retrieve Password
                                </button>
                            </div>
                        </form>

                        <!-- Register Link -->
                        <div class="text-center mt-4">
                            <p class="text-muted">Don't have an account?
                                <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Join ACATA</a>
                            </p>
                        </div>

                        <!-- Divider -->
                        <div class="position-relative my-4">
                            <hr>
                            <div class="position-absolute top-50 start-50 translate-middle bg-primary px-3 text-muted">
                                or
                            </div>
                        </div>

                        <!-- Additional Help -->
                        <div class="text-center mb-4">
                            <small class="text-muted">
                                Need help? <a href="{{ route('contact') }}" class="text-decoration-none">Contact support</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection