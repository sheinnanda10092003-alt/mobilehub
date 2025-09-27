@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Purchase Order #{{ $purchase->PurchaseID }}</h1>
    <div>
        <a href="{{ route('staff.purchases.show', $purchase->PurchaseID) }}" class="btn btn-outline-primary me-2">
            <i class="fas fa-eye me-2"></i>View Purchase
        </a>
        <a href="{{ route('staff.purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Purchases
        </a>
    </div>
</div>

<form id="purchaseForm" method="POST" action="{{ route('staff.purchases.update', $purchase->PurchaseID) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->SupplierID }}" 
                                            {{ (old('supplier_id', $purchase->SupplierID) == $supplier->SupplierID) ? 'selected' : '' }}>
                                        {{ $supplier->Name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" id="purchase_date" name="purchase_date" 
                                   class="form-control @error('purchase_date') is-invalid @enderror" 
                                   value="{{ old('purchase_date', $purchase->PurchaseDate->format('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" placeholder="Additional notes about this purchase...">{{ old('notes', $purchase->Notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Purchase Items</h5>
                    <button type="button" id="addItem" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        @foreach($purchase->items as $index => $item)
                            <div class="row item-row border rounded p-3 mb-3 bg-light">
                                <div class="col-md-5">
                                    <label class="form-label">Product <span class="text-danger">*</span></label>
                                    <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->ProductID }}" 
                                                    data-supplier="{{ $product->supplier->Name }}"
                                                    {{ $item->ProductID == $product->ProductID ? 'selected' : '' }}>
                                                {{ $product->Name }} ({{ $product->supplier->Name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="items[{{ $index }}][quantity]" 
                                           class="form-control quantity-input" 
                                           value="{{ $item->Quantity }}" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                                    <input type="number" name="items[{{ $index }}][unit_price]" 
                                           class="form-control price-input" 
                                           value="{{ $item->UnitPrice }}" min="0" step="0.01" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-item w-100">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="col-12 mt-2">
                                    <small class="text-muted">Item Total: <span class="item-total">${{ number_format($item->TotalPrice, 2) }}</span></small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="alert alert-info" id="noItemsAlert" style="{{ count($purchase->items) > 0 ? 'display: none;' : '' }}">
                        <i class="fas fa-info-circle me-2"></i>
                        Click "Add Item" to start adding products to this purchase order.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card sticky-top">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Items:</span>
                        <span id="totalItems">{{ count($purchase->items) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Quantity:</span>
                        <span id="totalQuantity">{{ $purchase->items->sum('Quantity') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total Amount:</strong>
                        <strong id="totalAmount">${{ number_format($purchase->TotalAmount, 2) }}</strong>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Update Purchase Order
                    </button>
                    
                    <a href="{{ route('staff.purchases.show', $purchase->PurchaseID) }}" class="btn btn-outline-secondary w-100 mb-2">
                        Cancel Changes
                    </a>
                    
                    <a href="{{ route('staff.purchases.index') }}" class="btn btn-outline-secondary w-100">
                        Back to Purchases
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Item Template (hidden) -->
<template id="itemTemplate">
    <div class="row item-row border rounded p-3 mb-3 bg-light">
        <div class="col-md-5">
            <label class="form-label">Product <span class="text-danger">*</span></label>
            <select name="items[][product_id]" class="form-select product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->ProductID }}" data-supplier="{{ $product->supplier->Name }}">
                        {{ $product->Name }} ({{ $product->supplier->Name }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="items[][quantity]" class="form-control quantity-input" min="1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Unit Price <span class="text-danger">*</span></label>
            <input type="number" name="items[][unit_price]" class="form-control price-input" min="0" step="0.01" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-item w-100">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="col-12 mt-2">
            <small class="text-muted">Item Total: <span class="item-total">$0.00</span></small>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let itemCounter = {{ count($purchase->items) }};

document.addEventListener('DOMContentLoaded', function() {
    const addItemBtn = document.getElementById('addItem');
    const itemsContainer = document.getElementById('itemsContainer');
    const noItemsAlert = document.getElementById('noItemsAlert');
    const itemTemplate = document.getElementById('itemTemplate');
    
    // Calculate initial totals
    updateOrderSummary();
    
    // Add item functionality
    addItemBtn.addEventListener('click', function() {
        addItem();
    });
    
    // Remove item functionality (event delegation)
    itemsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            removeItem(e.target.closest('.item-row'));
        }
    });
    
    // Calculate totals when inputs change (event delegation)
    itemsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
            updateItemTotal(e.target.closest('.item-row'));
            updateOrderSummary();
        }
    });
    
    // Form submission validation
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const itemRows = document.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase order.');
        }
    });
    
    function addItem() {
        const template = itemTemplate.content.cloneNode(true);
        const itemRow = template.querySelector('.item-row');
        
        // Update input names to include index
        const inputs = itemRow.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[]', `[${itemCounter}]`));
            }
        });
        
        itemsContainer.appendChild(template);
        itemCounter++;
        
        noItemsAlert.style.display = 'none';
        updateOrderSummary();
    }
    
    function removeItem(itemRow) {
        itemRow.remove();
        
        const itemRows = document.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            noItemsAlert.style.display = 'block';
        }
        
        updateOrderSummary();
    }
    
    function updateItemTotal(itemRow) {
        const quantityInput = itemRow.querySelector('.quantity-input');
        const priceInput = itemRow.querySelector('.price-input');
        const totalSpan = itemRow.querySelector('.item-total');
        
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        
        totalSpan.textContent = '$' + total.toFixed(2);
    }
    
    function updateOrderSummary() {
        const itemRows = document.querySelectorAll('.item-row');
        let totalItems = itemRows.length;
        let totalQuantity = 0;
        let totalAmount = 0;
        
        itemRows.forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            
            totalQuantity += quantity;
            totalAmount += quantity * price;
        });
        
        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('totalAmount').textContent = '$' + totalAmount.toFixed(2);
    }
});
</script>
@endpush