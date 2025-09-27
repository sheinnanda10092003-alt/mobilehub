@extends('customer.layouts.customer')

@section('title', 'Customer Login - MobileHub')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle"></i> Customer Login
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        
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
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <div class="position-relative">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required>
                                <button type="button" 
                                        class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2 p-1 password-toggle" 
                                        data-target="password"
                                        style="border: none; background: none; z-index: 10;">
                                    <i class="fas fa-eye" id="password-toggle-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 customer-btn">
                            <i class="fas fa-sign-in-alt"></i> Login to Account
                        </button>
                    </form>
                </div>
                
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        Don't have an account? 
                        <a href="{{ route('customer.signup') }}" class="text-primary">
                            <i class="fas fa-user-plus"></i> Sign up here
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Password Toggle CSS -->
<link rel="stylesheet" href="{{ asset('css/password-toggle.css') }}">

<!-- Enhanced Password Toggle JavaScript -->
<script src="{{ asset('js/password-toggle.js') }}"></script>

@endsection
