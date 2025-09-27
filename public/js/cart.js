// Cart functionality JavaScript
class Cart {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartCount();
        this.loadCartSidebar();
    }

    bindEvents() {
        // Add to cart buttons
        $(document).on('click', '.add-to-cart-btn', (e) => {
            e.preventDefault();
            this.addToCart(e.currentTarget);
        });

        // Cart modal/sidebar toggle
        $(document).on('click', '[data-bs-toggle="cart-modal"]', (e) => {
            e.preventDefault();
            this.showCartModal();
        });

        // Quick add to cart (without quantity selection)
        $(document).on('click', '.quick-add-cart', (e) => {
            e.preventDefault();
            const productId = $(e.currentTarget).data('product-id');
            this.quickAddToCart(productId);
        });

        // Update cart count on page load
        this.updateCartCount();
    }

    addToCart(button) {
        const $btn = $(button);
        const form = $btn.closest('form');
        const formData = new FormData(form[0]);
        const productId = $btn.data('product-id') || form.find('input[name="product_id"]').val();
        const quantity = formData.get('quantity') || 1;

        // Show loading state
        this.setButtonLoading($btn, true);

        $.ajax({
            url: `/cart/add/${productId}`,
            method: 'POST',
            data: {
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.handleAddToCartSuccess(response, $btn);
            },
            error: (xhr) => {
                this.handleAddToCartError(xhr, $btn);
            }
        });
    }

    quickAddToCart(productId, quantity = 1) {
        $.ajax({
            url: `/cart/add/${productId}`,
            method: 'POST',
            data: {
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.showToast('success', response.message);
                this.updateCartCount();
                this.updateCartSidebar();
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Error adding product to cart';
                this.showToast('error', message);
            }
        });
    }

    handleAddToCartSuccess(response, $btn) {
        this.setButtonLoading($btn, false);
        
        if (response.success) {
            // Show success message
            this.showToast('success', response.message);
            
            // Update cart count in navbar
            this.updateCartCount();
            
            // Update cart sidebar if visible
            this.updateCartSidebar();
            
            // Animate button
            this.animateButton($btn);
        }
    }

    handleAddToCartError(xhr, $btn) {
        this.setButtonLoading($btn, false);
        
        let message = 'Error adding product to cart';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }
        
        this.showToast('error', message);
    }

    setButtonLoading($btn, loading) {
        if (loading) {
            $btn.prop('disabled', true);
            $btn.find('.btn-text').hide();
            $btn.find('.loading-text').show();
            if ($btn.find('.loading-text').length === 0) {
                $btn.append('<span class="loading-text"><i class="fas fa-spinner fa-spin me-1"></i>Adding...</span>');
            }
        } else {
            $btn.prop('disabled', false);
            $btn.find('.btn-text').show();
            $btn.find('.loading-text').remove();
        }
    }

    animateButton($btn) {
        $btn.addClass('btn-success').removeClass('btn-primary');
        $btn.find('.btn-text').html('<i class="fas fa-check me-1"></i>Added!');
        
        setTimeout(() => {
            $btn.removeClass('btn-success').addClass('btn-primary');
            $btn.find('.btn-text').html('<i class="fas fa-cart-plus me-1"></i>Add to Cart');
        }, 2000);
    }

    updateCartCount() {
        if (!this.isAuthenticated()) {
            $('.cart-count').text('0');
            return;
        }

        $.get('/cart/count', (data) => {
            $('.cart-count').text(data.count);
            
            // Update cart badge visibility
            if (data.count > 0) {
                $('.cart-count').show();
            } else {
                $('.cart-count').hide();
            }
        }).fail(() => {
            $('.cart-count').text('0');
        });
    }

    updateCartSidebar() {
        if (!this.isAuthenticated()) {
            return;
        }

        $.get('/cart/items', (data) => {
            this.renderCartSidebar(data);
        });
    }

    renderCartSidebar(data) {
        const $cartSidebar = $('#cart-sidebar');
        if ($cartSidebar.length === 0) return;

        if (data.items.length === 0) {
            $cartSidebar.html(`
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Your cart is empty</p>
                </div>
            `);
            return;
        }

        let itemsHtml = '';
        data.items.forEach(item => {
            itemsHtml += `
                <div class="cart-item d-flex align-items-center mb-3 pb-3 border-bottom">
                    <img src="${item.image || '/images/no-image.png'}" 
                         alt="${item.name}" 
                         class="cart-item-image me-2" 
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 small">${item.name}</h6>
                        <small class="text-muted">₱${item.price} x ${item.quantity}</small>
                    </div>
                    <div class="text-end">
                        <small class="fw-bold">₱${item.total}</small>
                    </div>
                </div>
            `;
        });

        $cartSidebar.html(`
            <div class="cart-items">
                ${itemsHtml}
            </div>
            <div class="cart-footer pt-3 border-top">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total:</span>
                    <strong>₱${data.total}</strong>
                </div>
                <div class="d-grid gap-2">
                    <a href="/cart" class="btn btn-outline-primary btn-sm">View Cart</a>
                    <a href="/checkout" class="btn btn-primary btn-sm">Checkout</a>
                </div>
            </div>
        `);
    }

    showCartModal() {
        if (!this.isAuthenticated()) {
            this.showLoginModal();
            return;
        }

        // Create or show cart modal
        if ($('#cartModal').length === 0) {
            this.createCartModal();
        }

        this.updateCartModalContent();
        $('#cartModal').modal('show');
    }

    createCartModal() {
        const modalHtml = `
            <div class="modal fade" id="cartModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Shopping Cart</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="cart-modal-content">
                            <!-- Content will be loaded here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                            <a href="/cart" class="btn btn-outline-primary">View Full Cart</a>
                            <a href="/checkout" class="btn btn-primary">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('body').append(modalHtml);
    }

    updateCartModalContent() {
        const $content = $('#cart-modal-content');
        $content.html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

        $.get('/cart/items', (data) => {
            if (data.items.length === 0) {
                $content.html(`
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h6>Your cart is empty</h6>
                        <p class="text-muted">Start shopping to add items to your cart!</p>
                    </div>
                `);
                return;
            }

            let itemsHtml = '';
            data.items.forEach(item => {
                itemsHtml += `
                    <div class="row align-items-center mb-3 pb-3 border-bottom">
                        <div class="col-2">
                            <img src="${item.image || '/images/no-image.png'}" 
                                 alt="${item.name}" 
                                 class="img-fluid rounded" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">${item.name}</h6>
                            <small class="text-muted">₱${item.price} each</small>
                        </div>
                        <div class="col-2 text-center">
                            <span class="badge bg-secondary">${item.quantity}</span>
                        </div>
                        <div class="col-2 text-end">
                            <strong>₱${item.total}</strong>
                        </div>
                    </div>
                `;
            });

            $content.html(`
                <div class="cart-modal-items">
                    ${itemsHtml}
                </div>
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-6">
                        <strong>Total: ₱${data.total}</strong>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">${data.count} item${data.count !== 1 ? 's' : ''}</small>
                    </div>
                </div>
            `);
        });
    }

    loadCartSidebar() {
        // Load cart sidebar content on page load if element exists
        if ($('#cart-sidebar').length > 0) {
            this.updateCartSidebar();
        }
    }

    showToast(type, message, duration = 5000) {
        // Remove existing toasts
        $('.toast-container .toast').remove();

        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        // Create toast container if it doesn't exist
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }

        $('.toast-container').append(toastHtml);
        
        const $toast = $('.toast-container .toast').last();
        const toast = new bootstrap.Toast($toast[0], { delay: duration });
        toast.show();
    }

    showLoginModal() {
        // Show login modal or redirect to login
        if ($('#loginModal').length > 0) {
            $('#loginModal').modal('show');
        } else {
            window.location.href = '/customer/login';
        }
    }

    isAuthenticated() {
        // Check if user is authenticated
        return $('meta[name="customer-authenticated"]').attr('content') === 'true' ||
               $('.navbar .dropdown-toggle:contains("Profile")').length > 0;
    }
}

// Initialize cart when document is ready
$(document).ready(() => {
    window.cart = new Cart();
});

// Export for global use
window.Cart = Cart;