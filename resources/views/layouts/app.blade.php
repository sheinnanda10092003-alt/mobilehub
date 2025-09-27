<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MobileHub Staff Panel')</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        .logout-btn {
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .navbar-brand .badge {
            font-size: 0.65em;
            vertical-align: top;
        }
        .dropdown-item form {
            margin: 0;
        }
        .dropdown-item button {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 0.375rem 1rem;
        }
        
        /* Image loading improvements */
        .img-thumbnail, .product-image {
            border: 2px solid #dee2e6;
            transition: opacity 0.3s ease;
        }
        .img-thumbnail:hover {
            border-color: #0d6efd;
        }
        .loading-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('staff.dashboard') }}">
                <i class="fas fa-cog"></i> MobileHub 
                <span class="badge bg-warning text-dark">Staff Panel</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest('staff')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.login') }}">
                                <i class="fas fa-sign-in-alt"></i> Staff Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('staff.signup') }}">
                                <i class="fas fa-user-plus"></i> Staff Signup
                            </a>
                        </li>
                    @else
                        <!-- Desktop: User Dropdown -->
                        <li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> {{ auth('staff')->user()->StaffName ?? 'Staff' }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('staff.products.index') }}"><i class="fas fa-mobile-alt"></i> Products</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('staff.logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger logout-btn" type="submit">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Mobile: Individual Menu Items -->
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="{{ route('staff.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="{{ route('staff.products.index') }}">
                                <i class="fas fa-mobile-alt"></i> Products
                            </a>
                        </li>
                        <li class="nav-item d-lg-none">
                            <form method="POST" action="{{ route('staff.logout') }}" class="d-inline">
                                @csrf
                                <button class="nav-link btn btn-link text-danger border-0 bg-transparent" type="submit">
                                    <i class="fas fa-sign-out-alt"></i> Logout ({{ auth('staff')->user()->StaffName ?? 'Staff' }})
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4 container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>