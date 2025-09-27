@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Customer Management</h1>
    <a href="{{ route('staff.customers.export') }}" class="btn btn-outline-success">
        <i class="fas fa-download me-2"></i>Export CSV
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Search Customers</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Name, email, or phone...">
            </div>
            <div class="col-md-3">
                <label for="sort" class="form-label">Sort By</label>
                <select class="form-select" name="sort">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                    <option value="FirstName" {{ request('sort') == 'FirstName' ? 'selected' : '' }}>Name</option>
                    <option value="Email" {{ request('sort') == 'Email' ? 'selected' : '' }}>Email</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="direction" class="form-label">Order</label>
                <select class="form-select" name="direction">
                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Search
                </button>
                <a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Customer Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $customers->total() }}</h5>
                <p class="card-text">Total Customers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success">{{ $customers->where('orders_count', '>', 0)->count() }}</h5>
                <p class="card-text">Active Customers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-warning">{{ $customers->where('created_at', '>=', now()->startOfMonth())->count() }}</h5>
                <p class="card-text">New This Month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-info">{{ number_format($customers->avg('orders_count'), 1) }}</h5>
                <p class="card-text">Avg Orders</p>
            </div>
        </div>
    </div>
</div>

<!-- Customer Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i>Customer List
            <small class="text-muted">({{ $customers->total() }} total)</small>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Registration</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-3" 
                                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($customer->FirstName, 0, 1) }}{{ substr($customer->LastName, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $customer->FirstName }} {{ $customer->LastName }}</strong>
                                            <br><small class="text-muted">ID: {{ $customer->CustomerID }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="fas fa-envelope me-1"></i>{{ $customer->Email }}
                                        @if($customer->Phone)
                                            <br><i class="fas fa-phone me-1"></i>{{ $customer->Phone }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small>{{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}</small>
                                    <br><small class="text-muted">{{ $customer->created_at ? $customer->created_at->diffForHumans() : 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $customer->orders_count }}</span>
                                    @if($customer->orders_count > 0)
                                        <br><small class="text-success">Active</small>
                                    @else
                                        <br><small class="text-muted">No orders</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalSpent = $customer->orders->where('Status', '!=', 'cancelled')->sum('TotalAmount');
                                    @endphp
                                    <strong>${{ number_format($totalSpent, 2) }}</strong>
                                    @if($totalSpent > 0)
                                        <br><small class="text-muted">${{ number_format($totalSpent / max($customer->orders_count, 1), 2) }} avg</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('staff.customers.show', $customer->CustomerID) }}" 
                                           class="btn btn-outline-primary btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($customer->orders_count > 0)
                                            <a href="{{ route('staff.orders.index', ['customer' => $customer->CustomerID]) }}" 
                                               class="btn btn-outline-secondary btn-sm" title="View Orders">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </div>
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>No customers found</h5>
                <p class="text-muted">No customers match your search criteria.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar {
        font-size: 14px;
        font-weight: 600;
    }
</style>
@endpush