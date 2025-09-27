@extends('layouts.app')

@section('title', 'Staff Signup')

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <h2>Staff Signup</h2>

    <form method="POST" action="{{ route('staff.signup.post') }}">
        @csrf

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" name="username" type="text" class="form-control" required value="{{ old('username') }}">
            @error('username') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="staff_name" class="form-label">Staff Name</label>
            <input id="staff_name" name="staff_name" type="text" class="form-control" required value="{{ old('staff_name') }}">
            @error('staff_name') <div class="text-danger">{{ $message }}</div> @enderror
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
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="position-relative">
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                <button type="button" 
                        class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2 p-1 password-toggle" 
                        data-target="password_confirmation"
                        style="border: none; background: none; z-index: 10;">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        
        <div class="text-center mt-3">
            <a href="{{ route('staff.login') }}">Already have an account? Login</a>
        </div>
    </form>
</div>

<!-- Enhanced Password Validation CSS -->
<link rel="stylesheet" href="{{ asset('css/password-validation.css') }}">
<link rel="stylesheet" href="{{ asset('css/password-toggle.css') }}">

<!-- Enhanced Password Validation JavaScript -->
<script src="{{ asset('js/password-toggle.js') }}"></script>
<script src="{{ asset('js/password-validation.js') }}"></script>

@endsection
