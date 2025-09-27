@extends('customer.layouts.customer')

@section('title', 'Order #' . $order->OrderID)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Order #{{ $order->OrderID }}</h2>
                    <p class="text-muted mb-0">Placed on {{ $order->OrderDate instanceof \Carbon\Carbon ? $order->OrderDate->format('F d, Y \a\t g:i A') : $order->OrderDate }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $order->Status === 'pending' ? 'warning' : ($order->Status === 'delivered' ? 'success' : 'primary') }} fs-6 mb-2">
                        {{ ucfirst($order->Status) }}
                    </span>
                    <br>
                    <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Orders
                    </a>
                </div>
            </div>

            <!-- Order Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>Order Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-horizontal">
                        <div class="timeline-step {{ $order->Status ? 'active' : '' }}">
                            <div class="timeline-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Order Placed</h6>
                                <small>{{ $order->OrderDate instanceof \Carbon\Carbon ? $order->OrderDate->format('M d, g:i A') : $order->OrderDate }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-step {{ in_array($order->Status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Processing</h6>
                                <small>{{ $order->Status === 'processing' ? 'In progress' : (in_array($order->Status, ['shipped', 'delivered']) ? 'Completed' : 'Pending') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-step {{ in_array($order->Status, ['shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Shipped</h6>
                                <small>{{ $order->Status === 'shipped' ? 'In transit' : ($order->Status === 'delivered' ? 'Completed' : 'Pending') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-step {{ $order->Status === 'delivered' ? 'active' : '' }}">
                            <div class="timeline-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Delivered</h6>
                                <small>{{ $order->Status === 'delivered' ? 'Completed' : 'Pending' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                        <div class="row align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="col-2">
                                @if($item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" 
                                         alt="{{ $item->product->Name }}" 
                                         class="img-fluid rounded" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-mobile-alt text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-6">
                                <h6 class="mb-1">{{ $item->product->Name }}</h6>
                                <small class="text-muted">{{ Str::limit($item->product->Description, 100) }}</small>
                                <br>
                                <small class="text-muted">Unit Price: ₱{{ number_format($item->UnitPrice, 2) }}</small>
                            </div>
                            <div class="col-2 text-center">
                                <span class="badge bg-secondary">{{ $item->Quantity }}x</span>
                            </div>
                            <div class="col-2 text-end">
                                <strong>₱{{ number_format($item->UnitPrice * $item->Quantity, 2) }}</strong>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="row">
                        <div class="col-8 offset-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td class="border-0 text-end"><strong>Subtotal:</strong></td>
                                        <td class="border-0 text-end">₱{{ number_format($order->TotalAmount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border-0 text-end"><strong>Shipping:</strong></td>
                                        <td class="border-0 text-end text-success">FREE</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="text-end"><strong>Total:</strong></td>
                                        <td class="text-end"><strong class="text-primary">₱{{ number_format($order->TotalAmount, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Info -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-truck me-2"></i>Shipping Address
                            </h6>
                        </div>
                        <div class="card-body">
                            <address class="mb-0">
                                <strong>{{ $order->shipping_name }}</strong><br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                {{ $order->shipping_country }}<br>
                                <i class="fas fa-phone me-1"></i>{{ $order->shipping_phone }}<br>
                                <i class="fas fa-envelope me-1"></i>{{ $order->shipping_email }}
                            </address>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Payment Information
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($order->payment)
                                <p class="mb-2">
                                    <strong>Payment Method:</strong><br>
                                    <span class="text-capitalize">{{ str_replace('_', ' ', $order->payment->payment_method) }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>Payment Status:</strong><br>
                                    <span class="badge bg-{{ $order->payment->payment_status === 'completed' ? 'success' : ($order->payment->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment->payment_status)) }}
                                    </span>
                                </p>
                                <p class="mb-2">
                                    <strong>Amount:</strong><br>
                                    ₱{{ number_format($order->payment->amount, 2) }}
                                </p>
                                @if($order->payment->transaction_id)
                                    <p class="mb-2">
                                        <strong>Transaction ID:</strong><br>
                                        <code class="small">{{ $order->payment->transaction_id }}</code>
                                    </p>
                                @endif
                                @if($order->payment->paid_at)
                                    <p class="mb-0">
                                        <strong>Paid On:</strong><br>
                                        {{ $order->payment->paid_at instanceof \Carbon\Carbon ? $order->payment->paid_at->format('M d, Y g:i A') : $order->payment->paid_at }}
                                    </p>
                                @endif
                            @else
                                <p class="text-muted mb-0">No payment information available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Order Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Order Actions -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <button class="btn btn-outline-primary w-100" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Print Order
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            @if($order->Status !== 'delivered' && $order->Status !== 'cancelled')
                                <button class="btn btn-outline-info w-100">
                                    <i class="fas fa-map-marker-alt me-1"></i>Track Order
                                </button>
                            @else
                                <button class="btn btn-outline-secondary w-100" disabled>
                                    <i class="fas fa-check me-1"></i>Order Complete
                                </button>
                            @endif
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('home') }}" class="btn btn-primary w-100">
                                <i class="fas fa-shopping-cart me-1"></i>Shop Again
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-horizontal {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    padding: 20px 0;
}

.timeline-horizontal::before {
    content: '';
    position: absolute;
    top: 35px;
    left: 12.5%;
    right: 12.5%;
    height: 2px;
    background: #e9ecef;
    z-index: 0;
}

.timeline-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.timeline-step.active .timeline-icon {
    background: #28a745;
    color: white;
}

.timeline-step.active ~ .timeline-step .timeline-icon {
    background: #e9ecef;
    color: #6c757d;
}

.timeline-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #28a745;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.2rem;
    border: 3px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-step:not(.active) .timeline-icon {
    background: #e9ecef;
    color: #6c757d;
}

.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 2px;
}

.timeline-content small {
    font-size: 0.75rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .timeline-horizontal {
        flex-direction: column;
        align-items: center;
    }
    
    .timeline-horizontal::before {
        top: 25px;
        left: 25px;
        right: auto;
        width: 2px;
        height: calc(100% - 50px);
    }
    
    .timeline-step {
        flex: none;
        width: 100%;
        text-align: left;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .timeline-icon {
        margin: 0 15px 0 0;
        flex-shrink: 0;
    }
    
    .timeline-content {
        text-align: left;
    }
}

.badge.fs-6 {
    font-size: 1rem !important;
}

@media print {
    .btn, .card-header, .timeline-horizontal, nav, footer {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endpush