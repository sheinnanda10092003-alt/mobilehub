@extends('customer.layouts.customer')

@section('title', 'My Orders')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Orders</h2>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                </a>
            </div>

            @if($orders->count() > 0)
                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Order #{{ $order->OrderID }}</h6>
                                    <span class="badge bg-{{ $order->Status === 'pending' ? 'warning' : ($order->Status === 'delivered' ? 'success' : 'primary') }}">
                                        {{ ucfirst($order->Status) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <small class="text-muted">Order Date</small>
                                            <p class="mb-0">{{ $order->OrderDate instanceof \Carbon\Carbon ? $order->OrderDate->format('M d, Y') : $order->OrderDate }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <small class="text-muted">Total Amount</small>
                                            <p class="mb-0 fw-bold text-success">₱{{ number_format($order->TotalAmount, 2) }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <small class="text-muted">Items</small>
                                            <p class="mb-0">{{ $order->orderItems->count() }} item{{ $order->orderItems->count() !== 1 ? 's' : '' }}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <small class="text-muted">Payment Status</small>
                                            @if($order->payment)
                                                <p class="mb-0">
                                                    <span class="badge bg-{{ $order->payment->payment_status === 'completed' ? 'success' : 'warning' }} small">
                                                        {{ ucfirst(str_replace('_', ' ', $order->payment->payment_status)) }}
                                                    </span>
                                                </p>
                                            @else
                                                <p class="mb-0 text-muted">No payment info</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Order Items Preview -->
                                    <div class="mb-3">
                                        <small class="text-muted">Items:</small>
                                        <div class="mt-1">
                                            @foreach($order->orderItems->take(2) as $item)
                                                <div class="d-flex align-items-center mb-1">
                                                    @if($item->product->image_url)
                                                        <img src="{{ $item->product->image_url }}" 
                                                             alt="{{ $item->product->Name }}" 
                                                             class="me-2 rounded" 
                                                             style="width: 30px; height: 30px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center me-2 rounded" 
                                                             style="width: 30px; height: 30px;">
                                                            <i class="fas fa-mobile-alt text-muted small"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <small>{{ Str::limit($item->product->Name, 30) }}</small>
                                                        <small class="text-muted"> ({{ $item->Quantity }}x)</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($order->orderItems->count() > 2)
                                                <small class="text-muted">
                                                    +{{ $order->orderItems->count() - 2 }} more item{{ $order->orderItems->count() - 2 !== 1 ? 's' : '' }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-truck me-1"></i>
                                            Delivery to {{ $order->shipping_address ? Str::limit($order->shipping_address, 30) : 'Address not available' }}
                                        </small>
                                        <a href="{{ route('customer.orders.show', $order->OrderID) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
                    <h4>No Orders Yet</h4>
                    <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-1"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.badge.small {
    font-size: 0.7rem;
}

@media (max-width: 576px) {
    .card-body .row > div {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush