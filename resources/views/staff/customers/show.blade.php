@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Customer Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('staff.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active">{{ $customer->FirstName }} {{ $customer->LastName }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Customer Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Customer Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3" 
                         style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold;">
                        {{ substr($customer->FirstName, 0, 1) }}{{ substr($customer->LastName, 0, 1) }}
                    </div>
                    <h4>{{ $customer->FirstName }} {{ $customer->LastName }}</h4>
                    <p class="text-muted">Customer ID: {{ $customer->CustomerID }}</p>
                </div>
                
                <div class="mb-3">
                    <strong>Email:</strong><br>
                    <a href="mailto:{{ $customer->Email }}">{{ $customer->Email }}</a>
                </div>
                
                @if($customer->Phone)
                <div class="mb-3">
                    <strong>Phone:</strong><br>
                    <a href="tel:{{ $customer->Phone }}">{{ $customer->Phone }}</a>
                </div>
                @endif
                
                <div class="mb-3">
                    <strong>Registration Date:</strong><br>
                    {{ $customer->created_at ? $customer->created_at->format('M d, Y H:i A') : 'N/A' }}
                    @if($customer->created_at)
                        <small class="text-muted d-block">{{ $customer->created_at->diffForHumans() }}</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $customerStats['total_orders'] }}</h4>
                        <small class="text-muted">Total Orders</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">${{ number_format($customerStats['total_spent'], 2) }}</h4>
                        <small class="text-muted">Total Spent</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-warning">{{ $customerStats['pending_orders'] }}</h5>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-info">{{ $customerStats['completed_orders'] }}</h5>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
                @if($customerStats['last_order_date'])
                <hr>
                <div class="text-center">
                    <strong>Last Order:</strong><br>
                    <small>{{ \Carbon\Carbon::parse($customerStats['last_order_date'])->format('M d, Y') }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Orders History -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Order History
                    <span class="badge bg-primary">{{ $customer->orders->count() }}</span>
                </h5>
                @if($customer->orders->count() > 0)
                <a href="{{ route('staff.orders.index', ['customer' => $customer->CustomerID]) }}" 
                   class="btn btn-outline-primary btn-sm">
                    View All Orders
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($customer->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->orders->take(10) as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->OrderID }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</small>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($order->OrderDate)->format('H:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $order->orderItems->count() }} items</span>
                                        @if($order->orderItems->count() > 0)
                                            <br><small class="text-muted">{{ $order->orderItems->sum('Quantity') }} units</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($order->TotalAmount, 2) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($order->Status) {
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'shipped' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($order->Status) }}</span>
                                    </td>
                                    <td>
                                        @if($order->payment)
                                            @php
                                                $paymentClass = match($order->payment->PaymentStatus) {
                                                    'paid' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $paymentClass }}">{{ ucfirst($order->payment->PaymentStatus) }}</span>
                                            <br><small class="text-muted">{{ ucfirst($order->payment->PaymentMethod) }}</small>
                                        @else
                                            <span class="badge bg-secondary">No Payment</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('staff.orders.show', $order->OrderID) }}" 
                                           class="btn btn-outline-primary btn-sm" title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($customer->orders->count() > 10)
                    <div class="card-footer text-center">
                        <a href="{{ route('staff.orders.index', ['customer' => $customer->CustomerID]) }}" 
                           class="btn btn-outline-primary">
                            View All {{ $customer->orders->count() }} Orders
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5>No Orders Yet</h5>
                        <p class="text-muted">This customer hasn't placed any orders.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        @if($customer->orders->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Recent Activity
                </h5>
            </div>
            <div class="card-body">
                @php
                    $recentOrders = $customer->orders->sortByDesc('OrderDate')->take(3);
                @endphp
                @foreach($recentOrders as $order)
                <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                    <div class="me-3">
                        @php
                            $iconClass = match($order->Status) {
                                'completed' => 'fas fa-check-circle text-success',
                                'cancelled' => 'fas fa-times-circle text-danger',
                                'shipped' => 'fas fa-truck text-primary',
                                'processing' => 'fas fa-cog text-info',
                                default => 'fas fa-clock text-warning'
                            };
                        @endphp
                        <i class="{{ $iconClass }}"></i>
                    </div>
                    <div class="flex-fill">
                        <strong>Order #{{ $order->OrderID }}</strong>
                        <span class="badge bg-{{ match($order->Status) { 'pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', default => 'secondary' } }}">
                            {{ ucfirst($order->Status) }}
                        </span>
                        <br>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($order->OrderDate)->diffForHumans() }} • 
                            ${{ number_format($order->TotalAmount, 2) }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar {
        font-weight: 600;
    }
</style>
@endpush