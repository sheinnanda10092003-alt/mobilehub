<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="customer-authenticated" content="{{ auth('web')->check() ? 'true' : 'false' }}">
    <title>@yield('title', 'MobileHub - Buy Mobile Phones')</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        .customer-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .customer-btn {
            transition: all 0.3s ease;
        }
        .customer-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .navbar-brand .badge {
            font-size: 0.65em;
            vertical-align: top;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        /* Enhanced Mobile-First Image Styling */
        .product-image-container {
            position: relative;
            width: 100%;
            height: 200px; /* Mobile first */
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Responsive image heights */
        @media (min-width: 576px) {
            .product-image-container {
                height: 220px;
            }
        }
        
        @media (min-width: 768px) {
            .product-image-container {
                height: 250px;
            }
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
            background: #fff;
            padding: 10px;
        }
        
        /* Loading states */
        .product-image-container.loading .product-image {
            opacity: 0;
        }
        
        .product-image-container.loaded .product-image {
            opacity: 1;
        }
        
        .product-image-container.error .product-image {
            display: none;
        }
        
        /* Loading placeholder */
        .loading-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #f8f9fa 25%, #e9ecef 25%, #e9ecef 50%, #f8f9fa 50%, #f8f9fa 75%, #e9ecef 75%, #e9ecef);
            background-size: 30px 30px;
            animation: loading-shimmer 1.5s linear infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .product-image-container.loading .loading-placeholder {
            opacity: 1;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e9ecef;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes loading-shimmer {
            0% { background-position: 0 0; }
            100% { background-position: 30px 30px; }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Image fallback styling */
        .image-fallback {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        
        .image-fallback.active,
        .product-image-container.error .image-fallback {
            display: flex;
        }
        
        .fallback-content {
            color: #6c757d;
            max-width: 150px;
        }
        
        .fallback-content i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #667eea;
        }
        
        .fallback-content .product-name {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0.5rem 0 0.25rem;
            color: #495057;
            line-height: 1.2;
        }
        
        .fallback-content small {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        
        /* Mobile touch enhancements */
        .product-card {
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }
        
        .product-card.touched,
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* Mobile-specific improvements */
        @media (max-width: 767.98px) {
            .product-card {
                margin-bottom: 1.5rem;
            }
            
            .fallback-content i {
                font-size: 2rem;
            }
            
            .fallback-content .product-name {
                font-size: 0.8rem;
            }
            
            .product-image-container {
                border-radius: 6px 6px 0 0;
            }
            
            .product-image {
                border-radius: 6px 6px 0 0;
            }
        }
        
        /* High-resolution display optimization */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .product-image {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }
    </style>
</head>
<body>
    <!-- Customer Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark customer-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-mobile-alt"></i> MobileHub 
                <span class="badge bg-light text-primary">Shop</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customerNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="customerNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">
                            <i class="fas fa-mobile-alt"></i> Products
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    @auth('web')
                        <!-- Cart -->
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('cart.index') }}" data-bs-toggle="cart-modal">
                                <i class="fas fa-shopping-cart"></i> Cart
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="font-size: 0.6rem; display: none;">0</span>
                            </a>
                        </li>
                    @endauth
                    
                    @guest('web')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.signup') }}">
                                <i class="fas fa-user-plus"></i> Sign Up
                            </a>
                        </li>
                    @else
                        <!-- Desktop: User Dropdown -->
                        <li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link dropdown-toggle" href="#" id="customerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> {{ auth('web')->user()->Name ?? 'Customer' }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="customerDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}"><i class="fas fa-list-alt"></i> My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('customer.logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger customer-btn" type="submit">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Mobile: Individual Menu Items -->
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                        </li>
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="{{ route('customer.orders') }}">
                                <i class="fas fa-list-alt"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item d-lg-none">
                            <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                                @csrf
                                <button class="nav-link btn btn-link text-danger border-0 bg-transparent" type="submit">
                                    <i class="fas fa-sign-out-alt"></i> Logout ({{ auth('web')->user()->Name ?? 'Customer' }})
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-mobile-alt"></i> MobileHub</h5>
                    <p class="text-muted">Your trusted mobile phone store</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        &copy; {{ date('Y') }} MobileHub. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart JS -->
    <script src="{{ asset('js/cart.js') }}"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>