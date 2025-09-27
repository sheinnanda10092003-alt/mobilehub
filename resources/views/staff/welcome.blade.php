@extends('staff.layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Staff Dashboard</h1>
            <p class="lead">Welcome to MobileHub Staff Panel</p>
            
            @auth('staff')
                <div class="alert alert-success d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-check"></i> Welcome back, <strong>{{ auth('staff')->user()->StaffName }}</strong>!
                    </div>
                    <form method="POST" action="{{ route('staff.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="fas fa-mobile-alt"></i> Products
                    </h5>
                    <p class="card-text">Manage mobile phones and inventory</p>
                    <a href="{{ route('staff.products.index') }}" class="btn btn-primary">
                        View Products
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </h5>
                    <p class="card-text">View and manage customer orders</p>
                    <a href="{{ route('staff.orders.index') }}" class="btn btn-secondary">
                        View Orders
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="fas fa-users"></i> Customers
                    </h5>
                    <p class="card-text">Manage customer accounts and data</p>
                    <a href="{{ route('staff.customers.index') }}" class="btn btn-secondary">
                        View Customers
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line"></i> Reports
                    </h5>
                    <p class="card-text">Sales and inventory reports</p>
                    <a href="{{ route('staff.reports.index') }}" class="btn btn-secondary">
                        View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('staff.products.create') }}" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-plus"></i> Add New Product
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('staff.orders.index') }}" class="btn btn-info btn-block mb-2">
                                <i class="fas fa-eye"></i> View Recent Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection