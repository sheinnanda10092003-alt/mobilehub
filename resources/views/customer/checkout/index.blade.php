@extends('customer.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                @csrf
                
                <!-- Shipping Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Shipping Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shipping_name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control @error('shipping_name') is-invalid @enderror" 
                                       id="shipping_name" 
                                       name="shipping_name" 
                                       value="{{ old('shipping_name', $customer->Name) }}" 
                                       required>
                                @error('shipping_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control @error('shipping_email') is-invalid @enderror" 
                                       id="shipping_email" 
                                       name="shipping_email" 
                                       value="{{ old('shipping_email', $customer->Email) }}" 
                                       required>
                                @error('shipping_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_phone" class="form-label">Phone Number *</label>
                                <input type="tel" 
                                       class="form-control @error('shipping_phone') is-invalid @enderror" 
                                       id="shipping_phone" 
                                       name="shipping_phone" 
                                       value="{{ old('shipping_phone') }}" 
                                       required>
                                @error('shipping_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_country" class="form-label">Country *</label>
                                <input type="text" 
                                       class="form-control @error('shipping_country') is-invalid @enderror" 
                                       id="shipping_country" 
                                       name="shipping_country" 
                                       value="{{ old('shipping_country', 'Philippines') }}" 
                                       required>
                                @error('shipping_country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="shipping_address" class="form-label">Street Address *</label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                          id="shipping_address" 
                                          name="shipping_address" 
                                          rows="3" 
                                          required>{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shipping_city" class="form-label">City *</label>
                                <input type="text" 
                                       class="form-control @error('shipping_city') is-invalid @enderror" 
                                       id="shipping_city" 
                                       name="shipping_city" 
                                       value="{{ old('shipping_city') }}" 
                                       required>
                                @error('shipping_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shipping_state" class="form-label">State/Province *</label>
                                <input type="text" 
                                       class="form-control @error('shipping_state') is-invalid @enderror" 
                                       id="shipping_state" 
                                       name="shipping_state" 
                                       value="{{ old('shipping_state') }}" 
                                       required>
                                @error('shipping_state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shipping_zip" class="form-label">ZIP/Postal Code *</label>
                                <input type="text" 
                                       class="form-control @error('shipping_zip') is-invalid @enderror" 
                                       id="shipping_zip" 
                                       name="shipping_zip" 
                                       value="{{ old('shipping_zip') }}" 
                                       required>
                                @error('shipping_zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        @error('payment_method')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        
                        <div class="row">
                            @foreach($paymentMethods as $key => $method)
                                <div class="col-md-6 mb-3">
                                    <div class="card payment-method-card h-100" data-method="{{ $key }}">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input payment-method-radio" 
                                                       type="radio" 
                                                       name="payment_method" 
                                                       id="payment_{{ $key }}" 
                                                       value="{{ $key }}"
                                                       {{ old('payment_method') == $key ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="payment_{{ $key }}">
                                                    <div class="payment-icon mb-2">
                                                        @if($key === 'cash_on_delivery')
                                                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                                        @elseif($key === 'credit_card')
                                                            <i class="fas fa-credit-card fa-2x text-primary"></i>
                                                        @elseif($key === 'debit_card')
                                                            <i class="fas fa-credit-card fa-2x text-info"></i>
                                                        @elseif($key === 'paypal')
                                                            <i class="fab fa-paypal fa-2x text-primary"></i>
                                                        @elseif($key === 'bank_transfer')
                                                            <i class="fas fa-university fa-2x text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <h6 class="mb-1">{{ $method }}</h6>
                                                    @if($key === 'cash_on_delivery')
                                                        <small class="text-muted">Pay when you receive</small>
                                                    @elseif($key === 'credit_card' || $key === 'debit_card')
                                                        <small class="text-muted">Secure card payment</small>
                                                    @elseif($key === 'paypal')
                                                        <small class="text-muted">Fast & secure</small>
                                                    @elseif($key === 'bank_transfer')
                                                        <small class="text-muted">Direct bank transfer</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Order Notes (Optional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Any special instructions for your order...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <!-- Order Items -->
                    <div class="mb-3">
                        @foreach($cartItems as $item)
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                @if($item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" 
                                         alt="{{ $item->product->Name }}" 
                                         class="img-thumbnail me-2" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-mobile-alt text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small">{{ Str::limit($item->product->Name, 20) }}</h6>
                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                </div>
                                <div class="text-end">
                                    <small>₱{{ number_format($item->total, 2) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">FREE</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span>₱0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total:</strong>
                        <strong>₱{{ number_format($total, 2) }}</strong>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button type="submit" form="checkout-form" class="btn btn-success w-100 mb-3" id="place-order-btn">
                        <i class="fas fa-lock me-1"></i> Place Order
                    </button>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Your payment information is secure and encrypted
                        </small>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->has('stock'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first('stock') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->has('checkout'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first('checkout') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@endsection

@push('styles')
<style>
.payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.payment-method-card:hover {
    border-color: #007bff;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.075);
}

.payment-method-card.selected {
    border-color: #28a745;
    background-color: #f8f9fa;
    box-shadow: 0 0.125rem 0.25rem rgba(40, 167, 69, 0.1);
}

.form-check-input:checked ~ .form-check-label {
    color: #28a745;
}

.payment-method-radio {
    opacity: 0;
    position: absolute;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle payment method selection
    $('.payment-method-card').on('click', function() {
        let method = $(this).data('method');
        let radio = $(this).find('.payment-method-radio');
        
        // Uncheck all radios and remove selected class
        $('.payment-method-radio').prop('checked', false);
        $('.payment-method-card').removeClass('selected');
        
        // Check clicked radio and add selected class
        radio.prop('checked', true);
        $(this).addClass('selected');
    });
    
    // Initialize selected payment method
    $('.payment-method-radio:checked').closest('.payment-method-card').addClass('selected');
    
    // Form submission
    $('#checkout-form').on('submit', function(e) {
        let paymentMethod = $('input[name="payment_method"]:checked').val();
        
        if (!paymentMethod) {
            e.preventDefault();
            alert('Please select a payment method.');
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#place-order-btn').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...');
    });
    
    // Auto-populate city/state based on ZIP (Philippines specific)
    $('#shipping_zip').on('blur', function() {
        let zip = $(this).val();
        // This is a placeholder for ZIP code validation
        // In a real app, you'd integrate with a postal service API
    });
});
</script>
@endpush