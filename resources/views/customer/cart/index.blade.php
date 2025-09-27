@extends('customer.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Shopping Cart</h4>
                </div>
                <div class="card-body">
                    @if($cartItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    @foreach($cartItems as $item)
                                        <tr data-cart-id="{{ $item->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->image_url)
                                                        <img src="{{ $item->product->image_url }}" 
                                                             alt="{{ $item->product->Name }}" 
                                                             class="img-thumbnail me-3" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 60px; height: 60px;">
                                                            <i class="fas fa-mobile-alt fa-lg text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1">{{ $item->product->Name }}</h6>
                                                        <small class="text-muted">{{ Str::limit($item->product->Description, 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>₱{{ number_format($item->price, 2) }}</td>
                                            <td>
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" 
                                                            type="button" 
                                                            data-action="decrease"
                                                            data-cart-id="{{ $item->id }}">-</button>
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center quantity-input" 
                                                           value="{{ $item->quantity }}" 
                                                           min="1" 
                                                           max="{{ $item->product->Stock }}"
                                                           data-cart-id="{{ $item->id }}">
                                                    <button class="btn btn-outline-secondary btn-sm quantity-btn" 
                                                            type="button" 
                                                            data-action="increase"
                                                            data-cart-id="{{ $item->id }}">+</button>
                                                </div>
                                                <small class="text-muted d-block mt-1">Stock: {{ $item->product->Stock }}</small>
                                            </td>
                                            <td class="item-total">₱{{ number_format($item->total, 2) }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm remove-item" 
                                                        data-cart-id="{{ $item->id }}"
                                                        title="Remove item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button class="btn btn-outline-danger" id="clear-cart">
                                <i class="fas fa-trash me-1"></i> Clear Cart
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Your cart is empty</h5>
                            <p class="text-muted">Start shopping to add items to your cart!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-1"></i> Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($cartItems->count() > 0)
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">₱{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="cart-total">₱{{ number_format($total, 2) }}</strong>
                        </div>
                        
                        <a href="{{ route('checkout.index') }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-credit-card me-1"></i> Proceed to Checkout
                        </a>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure Checkout with SSL Encryption
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update quantity when input changes
    $('.quantity-input').on('change', function() {
        let cartId = $(this).data('cart-id');
        let quantity = parseInt($(this).val());
        let max = parseInt($(this).attr('max'));
        
        if (quantity < 1) {
            quantity = 1;
            $(this).val(1);
        } else if (quantity > max) {
            quantity = max;
            $(this).val(max);
            alert('Only ' + max + ' items available in stock.');
        }
        
        updateCartItem(cartId, quantity);
    });
    
    // Quantity buttons
    $('.quantity-btn').on('click', function() {
        let cartId = $(this).data('cart-id');
        let action = $(this).data('action');
        let input = $(this).siblings('.quantity-input');
        let currentQty = parseInt(input.val());
        let max = parseInt(input.attr('max'));
        
        let newQty = currentQty;
        if (action === 'increase' && currentQty < max) {
            newQty = currentQty + 1;
        } else if (action === 'decrease' && currentQty > 1) {
            newQty = currentQty - 1;
        }
        
        if (newQty !== currentQty) {
            input.val(newQty);
            updateCartItem(cartId, newQty);
        }
    });
    
    // Remove item
    $('.remove-item').on('click', function() {
        if (confirm('Are you sure you want to remove this item from cart?')) {
            let cartId = $(this).data('cart-id');
            removeCartItem(cartId);
        }
    });
    
    // Clear cart
    $('#clear-cart').on('click', function() {
        if (confirm('Are you sure you want to clear your entire cart?')) {
            clearCart();
        }
    });
    
    function updateCartItem(cartId, quantity) {
        $.ajax({
            url: `/cart/${cartId}`,
            method: 'PUT',
            data: {
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update item total
                    $(`tr[data-cart-id="${cartId}"] .item-total`).text('₱' + response.item_total);
                    // Update cart totals
                    $('#cart-subtotal, #cart-total').text('₱' + response.cart_total);
                    // Update cart count in navbar
                    updateCartCount();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error updating cart. Please try again.');
            }
        });
    }
    
    function removeCartItem(cartId) {
        $.ajax({
            url: `/cart/${cartId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $(`tr[data-cart-id="${cartId}"]`).fadeOut(400, function() {
                        $(this).remove();
                        // Check if cart is empty
                        if ($('#cart-items tr').length === 0) {
                            location.reload();
                        } else {
                            // Update cart totals
                            $('#cart-subtotal, #cart-total').text('₱' + response.cart_total);
                        }
                    });
                    updateCartCount();
                }
            },
            error: function() {
                alert('Error removing item. Please try again.');
            }
        });
    }
    
    function clearCart() {
        $.ajax({
            url: '/cart',
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error clearing cart. Please try again.');
            }
        });
    }
    
    function updateCartCount() {
        $.get('/cart/count', function(data) {
            $('.cart-count').text(data.count);
        });
    }
});
</script>
@endpush