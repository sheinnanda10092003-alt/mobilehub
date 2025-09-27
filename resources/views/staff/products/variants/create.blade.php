@extends('staff.layouts.app')

@section('title', 'Add New Variant - ' . $product->Name)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Variant</h4>
                    <small class="text-muted">Creating variant for: <strong>{{ $product->Name }}</strong></small>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.products.variants.store', $product->ProductID) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Color" class="form-label">Color <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('Color') is-invalid @enderror" 
                                           id="Color" 
                                           name="Color" 
                                           value="{{ old('Color') }}" 
                                           placeholder="e.g., Black, White, Blue"
                                           required>
                                    @error('Color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter the color name for this variant</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="RAM" class="form-label">RAM <span class="text-danger">*</span></label>
                                    <select class="form-select @error('RAM') is-invalid @enderror" 
                                            id="RAM" 
                                            name="RAM" 
                                            required>
                                        <option value="">Select RAM</option>
                                        <option value="4GB" {{ old('RAM') == '4GB' ? 'selected' : '' }}>4GB</option>
                                        <option value="6GB" {{ old('RAM') == '6GB' ? 'selected' : '' }}>6GB</option>
                                        <option value="8GB" {{ old('RAM') == '8GB' ? 'selected' : '' }}>8GB</option>
                                        <option value="12GB" {{ old('RAM') == '12GB' ? 'selected' : '' }}>12GB</option>
                                        <option value="16GB" {{ old('RAM') == '16GB' ? 'selected' : '' }}>16GB</option>
                                        <option value="32GB" {{ old('RAM') == '32GB' ? 'selected' : '' }}>32GB</option>
                                    </select>
                                    @error('RAM')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Storage" class="form-label">Storage <span class="text-danger">*</span></label>
                                    <select class="form-select @error('Storage') is-invalid @enderror" 
                                            id="Storage" 
                                            name="Storage" 
                                            required>
                                        <option value="">Select Storage</option>
                                        <option value="64GB" {{ old('Storage') == '64GB' ? 'selected' : '' }}>64GB</option>
                                        <option value="128GB" {{ old('Storage') == '128GB' ? 'selected' : '' }}>128GB</option>
                                        <option value="256GB" {{ old('Storage') == '256GB' ? 'selected' : '' }}>256GB</option>
                                        <option value="512GB" {{ old('Storage') == '512GB' ? 'selected' : '' }}>512GB</option>
                                        <option value="1TB" {{ old('Storage') == '1TB' ? 'selected' : '' }}>1TB</option>
                                        <option value="2TB" {{ old('Storage') == '2TB' ? 'selected' : '' }}>2TB</option>
                                    </select>
                                    @error('Storage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('Price') is-invalid @enderror" 
                                               id="Price" 
                                               name="Price" 
                                               value="{{ old('Price') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00"
                                               required>
                                        @error('Price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="SKU" class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('SKU') is-invalid @enderror" 
                                           id="SKU" 
                                           name="SKU" 
                                           value="{{ old('SKU') }}" 
                                           placeholder="e.g., PHN-BLK-8GB-128GB"
                                           required>
                                    @error('SKU')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Unique identifier for this variant</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Stock" class="form-label">Initial Stock <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('Stock') is-invalid @enderror" 
                                           id="Stock" 
                                           name="Stock" 
                                           value="{{ old('Stock', 0) }}" 
                                           min="0" 
                                           required>
                                    @error('Stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ImageURL" class="form-label">Image URL (Optional)</label>
                            <input type="url" 
                                   class="form-control @error('ImageURL') is-invalid @enderror" 
                                   id="ImageURL" 
                                   name="ImageURL" 
                                   value="{{ old('ImageURL') }}" 
                                   placeholder="https://example.com/image.jpg">
                            @error('ImageURL')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: URL to variant-specific image</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="IsActive" 
                                       name="IsActive" 
                                       value="1"
                                       {{ old('IsActive', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="IsActive">
                                    Active (visible to customers)
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.products.variants.index', $product->ProductID) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Variant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Preview Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Preview</h6>
                </div>
                <div class="card-body">
                    <div class="row" id="variant-preview">
                        <div class="col-md-4">
                            <img id="preview-image" src="{{ $product->sanitized_image_url }}" 
                                 alt="Product Image" class="img-fluid rounded" 
                                 style="max-height: 200px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $product->Name }}</h5>
                            <div class="variant-details">
                                <span class="badge bg-secondary me-1" id="preview-color">Color</span>
                                <span class="badge bg-info me-1" id="preview-ram">RAM</span>
                                <span class="badge bg-warning me-1" id="preview-storage">Storage</span>
                            </div>
                            <div class="mt-2">
                                <strong class="text-success h4" id="preview-price">$0.00</strong>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">SKU: <span id="preview-sku">Not set</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live preview functionality
    const colorInput = document.getElementById('Color');
    const ramSelect = document.getElementById('RAM');
    const storageSelect = document.getElementById('Storage');
    const priceInput = document.getElementById('Price');
    const skuInput = document.getElementById('SKU');
    const imageInput = document.getElementById('ImageURL');
    
    const previewColor = document.getElementById('preview-color');
    const previewRam = document.getElementById('preview-ram');
    const previewStorage = document.getElementById('preview-storage');
    const previewPrice = document.getElementById('preview-price');
    const previewSku = document.getElementById('preview-sku');
    const previewImage = document.getElementById('preview-image');
    
    function updatePreview() {
        previewColor.textContent = colorInput.value || 'Color';
        previewRam.textContent = ramSelect.value || 'RAM';
        previewStorage.textContent = storageSelect.value || 'Storage';
        previewPrice.textContent = priceInput.value ? '$' + parseFloat(priceInput.value).toFixed(2) : '$0.00';
        previewSku.textContent = skuInput.value || 'Not set';
        
        if (imageInput.value) {
            previewImage.src = imageInput.value;
            previewImage.onerror = function() {
                this.src = '{{ $product->sanitized_image_url }}';
            };
        }
        
        // Auto-generate SKU if fields are filled
        if (colorInput.value && ramSelect.value && storageSelect.value && !skuInput.value) {
            const color = colorInput.value.substring(0, 3).toUpperCase();
            const ram = ramSelect.value.replace('GB', 'GB');
            const storage = storageSelect.value.replace('GB', 'GB').replace('TB', 'TB');
            skuInput.value = `PHN-${color}-${ram}-${storage}`;
            previewSku.textContent = skuInput.value;
        }
    }
    
    // Add event listeners
    [colorInput, ramSelect, storageSelect, priceInput, skuInput, imageInput].forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // Initial update
    updatePreview();
});
</script>
@endsection