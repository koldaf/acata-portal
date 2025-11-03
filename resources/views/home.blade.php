@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="gradient-bg text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to ACATA Portal</h1>
                <p class="lead mb-4">The premier platform for Computer Adaptive Testing professionals across Africa. Connect, collaborate, and advance assessment technology.</p>
                @guest
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 me-md-2">Join Our Community</a>
                    <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg px-4">Learn More</a>
                </div>
                @else
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="{{ route('member.dashboard') }}" class="btn btn-light btn-lg px-4 me-md-2">Go to Dashboard</a>
                    <a href="{{ route('members.directory') }}" class="btn btn-outline-light btn-lg px-4">Browse Directory</a>
                </div>
                @endguest
            </div>
            <div class="col-lg-6 text-center">
                <i class="bi bi-graph-up-arrow" style="font-size: 15rem; opacity: 0.8;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container px-4 py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="fw-bold">Member Benefits</h2>
                <p class="lead text-muted">Access exclusive resources and tools designed for CAT professionals</p>
            </div>
        </div>

        <div class="row g-4 py-5">
            <div class="col-md-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-people-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Professional Network</h4>
                        <p class="text-muted">Connect with CAT experts and researchers across Africa through our member directory.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success bg-gradient text-white rounded-3 mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-text fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Digital Certificates</h4>
                        <p class="text-muted">Download and manage your professional membership certificates instantly.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info bg-gradient text-white rounded-3 mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-event fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Events & Workshops</h4>
                        <p class="text-muted">Stay updated with the latest conferences, webinars, and training sessions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold text-primary">250+</h3>
                <p class="text-muted">Members</p>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold text-primary">15+</h3>
                <p class="text-muted">Countries</p>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold text-primary">50+</h3>
                <p class="text-muted">Resources</p>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <h3 class="fw-bold text-primary">12+</h3>
                <p class="text-muted">Events/Year</p>
            </div>
        </div>
    </div>
</section>
@endsection