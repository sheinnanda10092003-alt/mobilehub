@extends('layouts.app')

@section('title', 'Staff Login')

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="text-center mb-4">Staff Login</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('staff.login.post') }}">
        @csrf
        
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" name="username" type="text" class="form-control" required value="{{ old('username') }}">
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="position-relative">
                <input id="password" name="password" type="password" class="form-control" required>
                <button type="button" 
                        class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2 p-1 password-toggle" 
                        data-target="password"
                        style="border: none; background: none; z-index: 10;">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Login</button>
        
        <div class="text-center mt-3">
            <a href="{{ route('staff.signup') }}">Don't have an account? Register</a>
        </div>
    </form>
</div>

<!-- Enhanced Password Toggle CSS -->
<link rel="stylesheet" href="{{ asset('css/password-toggle.css') }}">

<!-- Enhanced Password Toggle JavaScript -->
<script src="{{ asset('js/password-toggle.js') }}"></script>

@endsection
