@extends('customer.layouts.customer')

@section('title', 'Customer Sign Up - MobileHub')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i> Create Customer Account
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter your full name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter your email address"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <div class="position-relative password-input-container">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Create a password"
                                       required>
                                <button type="button" 
                                        class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2 p-1 password-toggle" 
                                        data-target="password"
                                        style="border: none; background: none; z-index: 10;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <div class="position-relative password-input-container">
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm your password"
                                       required>
                                <button type="button" 
                                        class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2 p-1 password-toggle" 
                                        data-target="password_confirmation"
                                        style="border: none; background: none; z-index: 10;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
            
                        <button type="submit" class="btn btn-success w-100 customer-btn">
                            <i class="fas fa-user-plus"></i> Create My Account
                        </button>
                    </form>
                </div>
                
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        Already have an account? 
                        <a href="{{ route('customer.login') }}" class="text-primary">
                            <i class="fas fa-sign-in-alt"></i> Login here
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Password Validation CSS -->
<link rel="stylesheet" href="{{ asset('css/password-validation.css') }}">
<link rel="stylesheet" href="{{ asset('css/password-toggle.css') }}">

<!-- Enhanced Password Validation JavaScript -->
<script src="{{ asset('js/password-toggle.js') }}"></script>
<script src="{{ asset('js/password-validation.js') }}"></script>

@endsection
