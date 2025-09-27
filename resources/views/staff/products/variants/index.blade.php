@extends('staff.layouts.app')

@section('title', 'Product Variants - ' . $product->Name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Product Variants</h4>
                        <small class="text-muted">Managing variants for: <strong>{{ $product->Name }}</strong></small>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('staff.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                        <a href="{{ route('staff.products.variants.create', $product->ProductID) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Variant
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($product->variants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Color</th>
                                        <th>RAM</th>
                                        <th>Storage</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr>
                                            <td>
                                                @if($variant->image_url)
                                                    <img src="{{ $variant->image_url }}" alt="{{ $variant->Color }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-mobile-alt text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $variant->Color }}; color: white;">
                                                    {{ $variant->Color }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark">{{ $variant->RAM }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">{{ $variant->Storage }}</span>
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ $variant->SKU }}</code>
                                            </td>
                                            <td>
                                                <strong class="text-success">${{ number_format($variant->Price, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($variant->Stock > 10)
                                                    <span class="badge bg-success">{{ $variant->Stock }}</span>
                                                @elseif($variant->Stock > 0)
                                                    <span class="badge bg-warning">{{ $variant->Stock }}</span>
                                                @else
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($variant->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('staff.products.variants.edit', [$product->ProductID, $variant->VariantID]) }}" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('staff.products.variants.toggle', [$product->ProductID, $variant->VariantID]) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-outline-{{ $variant->is_active ? 'warning' : 'success' }}" 
                                                                title="{{ $variant->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas fa-{{ $variant->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('staff.products.variants.destroy', [$product->ProductID, $variant->VariantID]) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this variant?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Variants Found</h4>
                            <p class="text-muted">This product doesn't have any variants yet.</p>
                            <a href="{{ route('staff.products.variants.create', $product->ProductID) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Variant
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Summary Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Product Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Variants:</strong>
                            <p class="mb-0">{{ $product->variants->count() }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Active Variants:</strong>
                            <p class="mb-0">{{ $product->variants->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Stock:</strong>
                            <p class="mb-0">{{ $product->variants->sum('Stock') }} units</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Price Range:</strong>
                            <p class="mb-0">
                                @if($product->variants->count() > 0)
                                    ${{ number_format($product->variants->min('Price'), 2) }} - 
                                    ${{ number_format($product->variants->max('Price'), 2) }}
                                @else
                                    N/A
                                @endif
                            </p>
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
    // Auto refresh page after toggle/delete actions
    @if(session('success'))
        // Show success message and maybe refresh data
        console.log('{{ session('success') }}');
    @endif
</script>
@endsection