@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Product</h5>
                        <a href="{{ route('staff.products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
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

                    <form method="POST" action="{{ route('staff.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('Name') is-invalid @enderror" 
                                           id="Name" 
                                           name="Name" 
                                           value="{{ old('Name') }}" 
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
                                           value="{{ old('Price') }}" 
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
                                           value="{{ old('Stock') }}" 
                                           min="0" 
                                           required
                                           placeholder="0">
                                    @error('Stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                                    {{ old('SupplierID') == $supplier->SupplierID ? 'selected' : '' }}>
                                                {{ $supplier->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('SupplierID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control @error('Description') is-invalid @enderror" 
                                      id="Description" 
                                      name="Description" 
                                      rows="4"
                                      placeholder="Enter product description, features, specifications...">{{ old('Description') }}</textarea>
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
                                   value="{{ old('image_url') }}"
                                   placeholder="https://example.com/image.jpg">
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: Provide a valid URL to an image of the product.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image when URL is entered
document.getElementById('image_url').addEventListener('blur', function() {
    const url = this.value;
    const preview = document.getElementById('image_preview');
    
    if (url && isValidUrl(url)) {
        if (!preview) {
            const previewDiv = document.createElement('div');
            previewDiv.id = 'image_preview';
            previewDiv.className = 'mt-2';
            previewDiv.innerHTML = '<img src="' + url + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;" onerror="this.style.display=\'none\';">';
            this.parentNode.appendChild(previewDiv);
        } else {
            preview.innerHTML = '<img src="' + url + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;" onerror="this.style.display=\'none\';">';
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