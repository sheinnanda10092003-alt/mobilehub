@extends('staff.layouts.app')

@section('title', 'Order Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Management</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <button class="btn btn-outline-success" onclick="exportOrders()">
                <i class="fas fa-file-excel me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           placeholder="Order ID, Customer name, email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Order Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        @foreach($orderStatuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">All Payment Status</option>
                        @foreach($paymentStatuses as $key => $label)
                            <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Orders
                <span class="badge bg-primary ms-2">{{ $orders->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Payment Method</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->OrderID }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $order->customer->Name ?? $order->shipping_name }}</div>
                                            <small class="text-muted">{{ $order->customer->Email ?? $order->shipping_email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $order->OrderDate->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $order->OrderDate->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $order->orderItems->count() }} item{{ $order->orderItems->count() !== 1 ? 's' : '' }}</span>
                                    </td>
                                    <td class="fw-bold">₱{{ number_format($order->TotalAmount, 2) }}</td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                data-order-id="{{ $order->OrderID }}"
                                                style="min-width: 120px;">
                                            @foreach($orderStatuses as $key => $label)
                                                <option value="{{ $key }}" {{ $order->Status === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        @if($order->payment)
                                            <span class="badge bg-{{ $order->payment->payment_status === 'completed' ? 'success' : ($order->payment->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->payment->payment_status)) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Payment</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment)
                                            <small class="text-capitalize">
                                                {{ str_replace('_', ' ', $order->payment->payment_method) }}
                                            </small>
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('staff.orders.show', $order->OrderID) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" 
                                                    data-bs-toggle="dropdown">
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('staff.orders.show', $order->OrderID) }}">
                                                        <i class="fas fa-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if($order->payment)
                                                    <li>
                                                        <button class="dropdown-item payment-status-btn" 
                                                                data-order-id="{{ $order->OrderID }}"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#paymentStatusModal">
                                                            <i class="fas fa-credit-card me-2"></i>Update Payment
                                                        </button>
                                                    </li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-primary" href="#">
                                                        <i class="fas fa-print me-2"></i>Print Invoice
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
                        </small>
                    </div>
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5>No orders found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'payment_status']))
                            Try adjusting your filter criteria.
                        @else
                            Orders will appear here once customers start placing orders.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="paymentStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentStatusForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status" required>
                            @foreach($paymentStatuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID (Optional)</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                        <div class="form-text">Update or add a transaction ID for this payment</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Payment Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.status-select.updating {
    opacity: 0.6;
    pointer-events: none;
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzMzNzNkYyIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1kYXNoYXJyYXk9IjE1LjcxIDUuMjMiPgogICAgPGFuaW1hdGVUcmFuc2Zvcm0gYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIiB0eXBlPSJyb3RhdGUiIGR1cj0iMnMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiB2YWx1ZXM9IjAgMTIgMTI7MzYwIDEyIDEyIj48L2FuaW1hdGVUcmFuc2Zvcm0+CjwvY2lyY2xlPgo8L3N2Zz4=');
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 16px 16px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group .dropdown-menu {
    min-width: 160px;
}

.btn-group .dropdown-item {
    font-size: 0.875rem;
}

.status-select {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.status-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Alert animations */
.alert {
    animation: slideInRight 0.5s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .status-select {
        font-size: 0.8rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Set up CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Store original values when page loads
    $('.status-select').each(function() {
        $(this).data('original-value', $(this).val());
    });
    
    // Handle order status changes
    $('.status-select').on('change', function() {
        let orderId = $(this).data('order-id');
        let newStatus = $(this).val();
        let originalStatus = $(this).data('original-value');
        let selectElement = $(this);
        
        // Don't proceed if status hasn't actually changed
        if (newStatus === originalStatus) {
            return;
        }
        
        // Disable select while updating
        selectElement.prop('disabled', true);
        
        // Show loading state
        selectElement.addClass('updating');
        
        $.ajax({
            url: `{{ route('staff.orders.updateStatus', '') }}/${orderId}`,
            method: 'PUT',
            data: {
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                selectElement.prop('disabled', false);
                selectElement.removeClass('updating');
                
                // Update the original value
                selectElement.data('original-value', newStatus);
                
                // Show success message
                showAlert('success', 'Order status updated successfully!');
            },
            error: function(xhr) {
                selectElement.prop('disabled', false);
                selectElement.removeClass('updating');
                
                // Revert to original value
                selectElement.val(originalStatus);
                
                let errorMessage = 'Error updating order status. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                }
                
                console.error('Order status update error:', xhr);
                showAlert('danger', errorMessage);
            }
        });
    });
    
    // Handle payment status modal
    $('.payment-status-btn').on('click', function() {
        let orderId = $(this).data('order-id');
        let actionUrl = `{{ route('staff.orders.updatePaymentStatus', '') }}/${orderId}`;
        $('#paymentStatusForm').attr('action', actionUrl);
    });
    
    // Handle payment status form submission
    $('#paymentStatusForm').on('submit', function(e) {
        e.preventDefault();
        
        let formElement = $(this);
        let submitButton = formElement.find('button[type="submit"]');
        let originalButtonText = submitButton.text();
        
        // Disable submit button and show loading
        submitButton.prop('disabled', true).text('Updating...');
        
        let formData = {
            payment_status: $('#payment_status').val(),
            transaction_id: $('#transaction_id').val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT'
        };
        
        $.ajax({
            url: formElement.attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#paymentStatusModal').modal('hide');
                showAlert('success', 'Payment status updated successfully!');
                
                // Reset form
                formElement[0].reset();
                
                // Reload page to reflect changes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Error updating payment status. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                }
                
                console.error('Payment status update error:', xhr);
                showAlert('danger', errorMessage);
            },
            complete: function() {
                // Re-enable submit button
                submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });
});

function showAlert(type, message) {
    // Generate unique ID for this alert
    let alertId = 'alert-' + Date.now();
    
    let alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show position-fixed" role="alert" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Add alert to body
    $('body').append(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('#' + alertId).fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

function exportOrders() {
    // This would integrate with a CSV/Excel export functionality
    alert('Export functionality would be implemented here with a proper export library.');
}
</script>
@endpush