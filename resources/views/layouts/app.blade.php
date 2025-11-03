<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ACATA Portal') - Association for Computer Adaptive Testing in Africa</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        .active{
            background-color: #79489F !important;
        }
        .text-primary{
            color: #CA1268 !important;
        }
        .btn-outline-primary{
            border-color: #CA1268 !important;
            color: #CA1268 !important;
        }
        .btn-outline-primary:hover {
            background-color: #CA1268 !important;
            color: #fff !important;
        }
        .bg-primary, .btn-primary{
            background-color: #CA1268 !important;
            border-color: #CA1268 !important;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #CA1268 0%, #764ba2 100%);
        }
        .navbar-brand {
            font-weight: 700;
        }
        .feature-icon {
            width: 4rem;
            height: 4rem;
            border-radius: .75rem;
        }
        .card-hover {
            transition: transform 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-5px);
        }
        .footer {
            background-color: #f8f9fa;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gradient-bg shadow">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{-- - <i class="bi bi-graph-up"></i> ACATA Portal --}}
                <img src="{{ asset('img/acata-logo.png') }}" alt="ACATA Logo"/>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">About</a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link" href="{{ route('members.directory') }}">Directory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('events') }}">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('resources') }}">Resources</a>
                    </li>-->
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('member.dashboard') }}">Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('member.profile') }}">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Certificates</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light ms-2" href="{{ route('register') }}">Join ACATA</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5 py-4 border-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <h5>ACATA</h5>
                    <p class="text-muted">Association for Computer Adaptive Testing in Africa - Advancing educational assessment technology across the continent.</p>
                </div>
                <div class="col-lg-2 col-6 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-muted">Home</a></li>
                        <li><a href="{{ route('about') }}" class="text-muted">About</a></li>
                        <li><a href="{{ route('contact') }}" class="text-muted">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6 mb-3">
                    <h5>Members</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}" class="text-muted">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-muted">Register</a></li>
                        <li><a href="{{ route('resources') }}" class="text-muted">Resources</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-3">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-envelope me-2"></i> info@acata.org</li>
                        <li><i class="bi bi-globe me-2"></i> www.acata.org</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="d-flex flex-column flex-sm-row justify-content-between py-2">
                <p class="text-muted">&copy; 2024 ACATA. All rights reserved.</p>
                <ul class="list-unstyled d-flex">
                    <li class="ms-3"><a class="text-muted" href="#"><i class="bi bi-twitter"></i></a></li>
                    <li class="ms-3"><a class="text-muted" href="#"><i class="bi bi-linkedin"></i></a></li>
                    <li class="ms-3"><a class="text-muted" href="#"><i class="bi bi-facebook"></i></a></li>
                </ul>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme Toggle Script -->
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            const getPreferredTheme = () => {
                if (storedTheme) return storedTheme;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };
            
            const setTheme = function(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
            };
            
            setTheme(getPreferredTheme());
            
            window.addEventListener('DOMContentLoaded', () => {
                // Theme toggle functionality can be added here
            });
        })();
    </script>
    
    @stack('scripts')
</body>
</html>