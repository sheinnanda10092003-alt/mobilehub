@extends('staff.layouts.app')

@section('title', 'Order #' . $order->OrderID)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Order #{{ $order->OrderID }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('staff.orders.index') }}">Orders</a>
                    </li>
                    <li class="breadcrumb-item active">#{{ $order->OrderID }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Order Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>#{{ $order->OrderID }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Date:</strong></td>
                                    <td>{{ $order->OrderDate->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <form method="POST" action="{{ route('staff.orders.updateStatus', $order->OrderID) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                                                @foreach(App\Models\Order::getOrderStatuses() as $key => $label)
                                                    <option value="{{ $key }}" {{ $order->Status === $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="fw-bold text-primary">₱{{ number_format($order->TotalAmount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>{{ $order->customer->Name ?? 'Guest' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $order->customer->Email ?? $order->shipping_email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $order->shipping_phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Items:</strong></td>
                                    <td>{{ $order->orderItems->count() }} item{{ $order->orderItems->count() !== 1 ? 's' : '' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Stock Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" 
                                                         alt="{{ $item->product->Name }}" 
                                                         class="img-thumbnail me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-mobile-alt text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item->product->Name }}</h6>
                                                    <small class="text-muted">{{ Str::limit($item->product->Description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>PRD-{{ $item->product->ProductID }}</code>
                                        </td>
                                        <td>₱{{ number_format($item->UnitPrice, 2) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->Quantity }}</span>
                                        </td>
                                        <td class="fw-bold">₱{{ number_format($item->UnitPrice * $item->Quantity, 2) }}</td>
                                        <td>
                                            @if($item->product->Stock > 10)
                                                <span class="badge bg-success">In Stock ({{ $item->product->Stock }})</span>
                                            @elseif($item->product->Stock > 0)
                                                <span class="badge bg-warning">Low Stock ({{ $item->product->Stock }})</span>
                                            @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Subtotal:</th>
                                    <th>₱{{ number_format($order->TotalAmount, 2) }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Shipping:</th>
                                    <th class="text-success">FREE</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-primary">₱{{ number_format($order->TotalAmount, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>Shipping Address
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6>Delivery Address</h6>
                                <p class="mb-1"><strong>{{ $order->shipping_name }}</strong></p>
                                <p class="mb-1">{{ $order->shipping_address }}</p>
                                <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                                <p class="mb-0">{{ $order->shipping_country }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6>Contact Information</h6>
                                <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $order->shipping_email }}</p>
                                <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $order->shipping_phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Order Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Information -->
            @if($order->payment)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-3">
                            <tr>
                                <td><strong>Method:</strong></td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $order->payment->payment_method) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->payment->payment_status === 'completed' ? 'success' : ($order->payment->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment->payment_status)) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Amount:</strong></td>
                                <td class="fw-bold">₱{{ number_format($order->payment->amount, 2) }}</td>
                            </tr>
                            @if($order->payment->transaction_id)
                                <tr>
                                    <td><strong>Transaction ID:</strong></td>
                                    <td><code>{{ $order->payment->transaction_id }}</code></td>
                                </tr>
                            @endif
                            @if($order->payment->paid_at)
                                <tr>
                                    <td><strong>Paid At:</strong></td>
                                    <td>{{ $order->payment->paid_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            @endif
                        </table>

                        <!-- Update Payment Status -->
                        <form method="POST" action="{{ route('staff.orders.updatePaymentStatus', $order->OrderID) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Update Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-select">
                                    <option value="pending" {{ $order->payment->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $order->payment->payment_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ $order->payment->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ $order->payment->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control" value="{{ $order->payment->transaction_id }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i> Update Payment
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-1"></i> Send Email to Customer
                        </button>
                        <button class="btn btn-outline-info btn-sm">
                            <i class="fas fa-truck me-1"></i> Generate Shipping Label
                        </button>
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-invoice me-1"></i> Download Invoice
                        </button>
                        @if($order->Status !== 'cancelled')
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmCancellation()">
                                <i class="fas fa-times me-1"></i> Cancel Order
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Order Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Placed</h6>
                                <small class="text-muted">{{ $order->OrderDate->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                        
                        @if($order->payment && $order->payment->payment_status === 'completed')
                            <div class="timeline-item active">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Payment Confirmed</h6>
                                    <small class="text-muted">{{ $order->payment->paid_at ? $order->payment->paid_at->format('M d, Y g:i A') : 'N/A' }}</small>
                                </div>
                            </div>
                        @endif
                        
                        <div class="timeline-item {{ in_array($order->Status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker bg-{{ in_array($order->Status, ['processing', 'shipped', 'delivered']) ? 'success' : 'secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Processing</h6>
                                <small class="text-muted">{{ $order->Status === 'processing' ? 'Currently processing' : 'Pending' }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ in_array($order->Status, ['shipped', 'delivered']) ? 'active' : '' }}">
                            <div class="timeline-marker bg-{{ in_array($order->Status, ['shipped', 'delivered']) ? 'success' : 'secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Shipped</h6>
                                <small class="text-muted">{{ $order->Status === 'shipped' ? 'Currently shipped' : 'Pending' }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $order->Status === 'delivered' ? 'active' : '' }}">
                            <div class="timeline-marker bg-{{ $order->Status === 'delivered' ? 'success' : 'secondary' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Delivered</h6>
                                <small class="text-muted">{{ $order->Status === 'delivered' ? 'Delivered' : 'Pending' }}</small>
                            </div>
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
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -22px;
    top: 8px;
    bottom: -20px;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -27px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.active .timeline-marker {
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-content h6 {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.timeline-content small {
    font-size: 0.75rem;
}

@media print {
    .btn, .card-header, .timeline, .d-flex.justify-content-between, nav {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function confirmCancellation() {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        // Submit form to cancel order
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("staff.orders.updateStatus", $order->OrderID) }}';
        
        let csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        let methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        let statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = 'cancelled';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush