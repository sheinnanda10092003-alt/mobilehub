@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Product</h5>
                        <div class="btn-group">
                            <a href="{{ route('staff.products.show', $product->ProductID) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('staff.products.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.products.update', $product->ProductID) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Product ID:</strong> {{ $product->ProductID }}
                                    <span class="ms-3"><strong>Created:</strong> {{ $product->created_at ? $product->created_at->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('Name') is-invalid @enderror" 
                                           id="Name" 
                                           name="Name" 
                                           value="{{ old('Name', $product->Name) }}" 
                                           required
                                           placeholder="e.g., iPhone 15 Pro Max">
                                    @error('Name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('Price') is-invalid @enderror" 
                                           id="Price" 
                                           name="Price" 
                                           value="{{ old('Price', $product->Price) }}" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           placeholder="0.00">
                                    @error('Price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('Stock') is-invalid @enderror" 
                                           id="Stock" 
                                           name="Stock" 
                                           value="{{ old('Stock', $product->Stock) }}" 
                                           min="0" 
                                           required>
                                    @error('Stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Current stock: 
                                        @if($product->Stock > 10)
                                            <span class="text-success">{{ $product->Stock }} units (Good)</span>
                                        @elseif($product->Stock > 0)
                                            <span class="text-warning">{{ $product->Stock }} units (Low)</span>
                                        @else
                                            <span class="text-danger">Out of stock</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="SupplierID" class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select class="form-control @error('SupplierID') is-invalid @enderror" 
                                            id="SupplierID" 
                                            name="SupplierID" 
                                            required>
                                        <option value="">Select a supplier...</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->SupplierID }}" 
                                                    {{ (old('SupplierID', $product->SupplierID) == $supplier->SupplierID) ? 'selected' : '' }}>
                                                {{ $supplier->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('SupplierID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($product->supplier)
                                        <div class="form-text">
                                            Current supplier: <strong>{{ $product->supplier->Name }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control @error('Description') is-invalid @enderror" 
                                      id="Description" 
                                      name="Description" 
                                      rows="4"
                                      placeholder="Enter product description, features, specifications...">{{ old('Description', $product->Description) }}</textarea>
                            @error('Description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">Product Image URL</label>
                            <input type="url" 
                                   class="form-control @error('image_url') is-invalid @enderror" 
                                   id="image_url" 
                                   name="image_url" 
                                   value="{{ old('image_url', $product->image_url) }}"
                                   placeholder="https://example.com/image.jpg">
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: Provide a valid URL to an image of the product.</div>
                            
                            <!-- Current image preview -->
                            @if($product->image_url)
                                <div class="mt-2">
                                    <label class="form-label">Current Image:</label><br>
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->Name }}" 
                                         class="img-thumbnail"
                                         style="max-width: 200px; max-height: 200px;">
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('staff.products.show', $product->ProductID) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <a href="{{ route('staff.products.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> All Products
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Activity Card -->
            @if($product->orderItems->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Recent Order Activity</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->orderItems->take(5) as $orderItem)
                                        <tr>
                                            <td>#{{ $orderItem->OrderID }}</td>
                                            <td>{{ $orderItem->Quantity }}</td>
                                            <td>${{ number_format($orderItem->UnitPrice, 2) }}</td>
                                            <td>
                                                @if($orderItem->order)
                                                    {{ $orderItem->order->OrderDate ? \Carbon\Carbon::parse($orderItem->order->OrderDate)->format('M d') : 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Preview new image when URL is changed
document.getElementById('image_url').addEventListener('blur', function() {
    const url = this.value;
    let preview = document.getElementById('new_image_preview');
    
    if (url && isValidUrl(url)) {
        if (!preview) {
            const previewDiv = document.createElement('div');
            previewDiv.id = 'new_image_preview';
            previewDiv.className = 'mt-2';
            previewDiv.innerHTML = '<label class="form-label">New Image Preview:</label><br><img src="' + url + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;" onerror="this.style.display=\'none\';">';
            this.parentNode.appendChild(previewDiv);
        } else {
            preview.querySelector('img').src = url;
            preview.querySelector('img').style.display = 'block';
        }
    } else if (preview) {
        preview.remove();
    }
});

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}
</script>
@endsection