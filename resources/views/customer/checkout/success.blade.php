@extends('customer.layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h2 class="text-success">Order Placed Successfully!</h2>
                <p class="text-muted">Thank you for your order. We'll send you a confirmation email shortly.</p>
            </div>

            <!-- Order Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Order Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-1"><strong>Order ID:</strong> #{{ $order->OrderID }}</p>
                            <p class="mb-1"><strong>Order Date:</strong> {{ $order->OrderDate instanceof \Carbon\Carbon ? $order->OrderDate->format('M d, Y g:i A') : $order->OrderDate }}</p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-{{ $order->Status === 'pending' ? 'warning' : ($order->Status === 'completed' ? 'success' : 'primary') }}">
                                    {{ ucfirst($order->Status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ $order->payment->payment_method ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Payment Status:</strong> 
                                <span class="badge bg-{{ $order->payment && $order->payment->payment_status === 'completed' ? 'success' : 'warning' }}">
                                    {{ $order->payment ? ucfirst(str_replace('_', ' ', $order->payment->payment_status)) : 'Pending' }}
                                </span>
                            </p>
                            @if($order->payment && $order->payment->transaction_id)
                                <p class="mb-1"><strong>Transaction ID:</strong> {{ $order->payment->transaction_id }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Shipping Address</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-1"><strong>{{ $order->shipping_name }}</strong></p>
                                <p class="mb-1">{{ $order->shipping_address }}</p>
                                <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                                <p class="mb-1">{{ $order->shipping_country }}</p>
                                <p class="mb-0"><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
                                <p class="mb-0"><strong>Email:</strong> {{ $order->shipping_email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-4">
                        <h6>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
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
                                            <td>₱{{ number_format($item->UnitPrice, 2) }}</td>
                                            <td>{{ $item->Quantity }}</td>
                                            <td>₱{{ number_format($item->UnitPrice * $item->Quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <th>₱{{ number_format($order->TotalAmount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Shipping:</th>
                                        <th class="text-success">FREE</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="text-primary">₱{{ number_format($order->TotalAmount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    @if($order->notes)
                        <div class="mb-4">
                            <h6>Order Notes</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Method Specific Information -->
                    @if($order->payment && $order->payment->payment_method === 'cash_on_delivery')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Cash on Delivery:</strong> Please have the exact amount ready when your order arrives. 
                            Our delivery person will collect ₱{{ number_format($order->TotalAmount, 2) }} upon delivery.
                        </div>
                    @elseif($order->payment && $order->payment->payment_status === 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Payment Confirmed:</strong> Your payment has been successfully processed. 
                            Your order is now being prepared for shipment.
                        </div>
                    @elseif($order->payment && $order->payment->payment_status === 'failed')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Payment Failed:</strong> There was an issue processing your payment. 
                            Please contact our customer service or try placing the order again.
                        </div>
                    @endif

                    <!-- Next Steps -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>What happens next?</h6>
                            <ul class="mb-0">
                                <li>You'll receive an email confirmation shortly</li>
                                @if($order->payment && $order->payment->payment_status === 'completed')
                                    <li>Your order is being prepared for shipment</li>
                                    <li>You'll receive a tracking number when your order ships</li>
                                @elseif($order->payment && $order->payment->payment_method === 'cash_on_delivery')
                                    <li>Your order will be prepared and dispatched soon</li>
                                    <li>Have the exact amount ready for delivery</li>
                                @else
                                    <li>Complete your payment to process your order</li>
                                @endif
                                <li>Estimated delivery: 3-5 business days</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('customer.orders.show', $order->OrderID) }}" class="btn btn-primary me-3">
                    <i class="fas fa-eye me-1"></i> Track Order
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-1"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection