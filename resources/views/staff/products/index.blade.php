@extends('staff.layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Products Management</h1>
                <a href="{{ route('staff.products.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Products</h5>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Variants</th>
                                        <th>Supplier</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $product->ProductID }}</td>
                            <td>
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->Name }}" 
                                         class="img-thumbnail"
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\"bg-light d-flex align-items-center justify-content-center\" style=\"width: 50px; height: 50px; border-radius: 4px;\"><i class=\"fas fa-mobile-alt text-muted\"></i></div>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; border-radius: 4px;">
                                        <i class="fas fa-mobile-alt text-muted"></i>
                                    </div>
                                @endif
                            </td>
                                            <td>
                                                <strong>{{ $product->Name }}</strong>
                                            </td>
                                            <td>
                                                {{ Str::limit($product->Description, 50) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">${{ number_format($product->Price, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($product->hasVariants())
                                                    @if($product->total_stock > 10)
                                                        <span class="badge bg-success">{{ $product->total_stock }} (variants)</span>
                                                    @elseif($product->total_stock > 0)
                                                        <span class="badge bg-warning">{{ $product->total_stock }} (variants)</span>
                                                    @else
                                                        <span class="badge bg-danger">Out of Stock (variants)</span>
                                                    @endif
                                                @else
                                                    @if($product->Stock > 10)
                                                        <span class="badge bg-success">{{ $product->Stock }}</span>
                                                    @elseif($product->Stock > 0)
                                                        <span class="badge bg-warning">{{ $product->Stock }}</span>
                                                    @else
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->hasVariants())
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-info me-2">{{ $product->variants->count() }}</span>
                                                        <a href="{{ route('staff.products.variants.index', $product->ProductID) }}" 
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-cogs"></i> Manage
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-muted me-2">None</span>
                                                        <a href="{{ route('staff.products.variants.create', $product->ProductID) }}" 
                                                           class="btn btn-outline-success btn-sm">
                                                            <i class="fas fa-plus"></i> Add
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->supplier)
                                                    {{ $product->supplier->Name }}
                                                @else
                                                    <span class="text-muted">No Supplier</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('staff.products.show', $product->ProductID) }}" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('staff.products.edit', $product->ProductID) }}" 
                                                       class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $product->ProductID }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $product->ProductID }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Delete Product</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete <strong>{{ $product->Name }}</strong>?
                                                                <br><small class="text-muted">This action cannot be undone.</small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form method="POST" action="{{ route('staff.products.destroy', $product->ProductID) }}" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                            <h5>No Products Found</h5>
                            <p class="text-muted">Start by adding your first product to the inventory.</p>
                            <a href="{{ route('staff.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection