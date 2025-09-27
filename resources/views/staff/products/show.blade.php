@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Product Details</h1>
                <div class="btn-group">
                    <a href="{{ route('staff.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                    <a href="{{ route('staff.products.edit', $product->ProductID) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Product Image</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->Name }}" 
                                     class="img-fluid rounded"
                                     style="max-height: 300px; width: auto;"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\"bg-light d-flex align-items-center justify-content-center rounded\" style=\"height: 300px;\"><div class=\"text-center\"><i class=\"fas fa-mobile-alt fa-4x text-muted mb-2\"></i><p class=\"text-muted mb-0\">Image could not be loaded</p><small class=\"text-muted\">{{ $product->Name }}</small></div></div>';">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 300px;">
                                    <div class="text-center">
                                        <i class="fas fa-mobile-alt fa-4x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No image available</p>
                                        <small class="text-muted">{{ $product->Name }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Product Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Product ID:</th>
                                    <td><span class="badge bg-secondary">{{ $product->ProductID }}</span></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><h4 class="mb-0">{{ $product->Name }}</h4></td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>
                                        @if($product->Description)
                                            <p class="mb-0">{{ $product->Description }}</p>
                                        @else
                                            <span class="text-muted">No description available</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td>
                                        <span class="h4 text-success">
                                            ${{ number_format($product->Price, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stock:</th>
                                    <td>
                                        @if($product->Stock > 10)
                                            <span class="badge bg-success fs-6">{{ $product->Stock }} units</span>
                                            <small class="text-success ms-2">In Stock</small>
                                        @elseif($product->Stock > 0)
                                            <span class="badge bg-warning fs-6">{{ $product->Stock }} units</span>
                                            <small class="text-warning ms-2">Low Stock</small>
                                        @else
                                            <span class="badge bg-danger fs-6">Out of Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Supplier:</th>
                                    <td>
                                        @if($product->supplier)
                                            <strong>{{ $product->supplier->Name }}</strong><br>
                                            <small class="text-muted">
                                                @if($product->supplier->Contact)
                                                    Contact: {{ $product->supplier->Contact }}<br>
                                                @endif
                                                @if($product->supplier->Address)
                                                    Address: {{ $product->supplier->Address }}
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">No supplier assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $product->created_at ? $product->created_at->format('M d, Y g:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $product->updated_at ? $product->updated_at->format('M d, Y g:i A') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory History Section (if available) -->
            @if($product->inventories->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Inventory History</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Inventory ID</th>
                                                <th>Quantity</th>
                                                <th>Updated At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->inventories->take(10) as $inventory)
                                                <tr>
                                                    <td>{{ $inventory->InventoryID }}</td>
                                                    <td>{{ $inventory->Quantity }}</td>
                                                    <td>{{ $inventory->UpdatedAt ?? $inventory->updated_at }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Order Items Section (if available) -->
            @if($product->orderItems->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Orders</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th>Order Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->orderItems->take(10) as $orderItem)
                                                <tr>
                                                    <td>{{ $orderItem->OrderID }}</td>
                                                    <td>{{ $orderItem->Quantity }}</td>
                                                    <td>${{ number_format($orderItem->UnitPrice, 2) }}</td>
                                                    <td>${{ number_format($orderItem->UnitPrice * $orderItem->Quantity, 2) }}</td>
                                                    <td>
                                                        @if($orderItem->order)
                                                            {{ $orderItem->order->OrderDate ? \Carbon\Carbon::parse($orderItem->order->OrderDate)->format('M d, Y') : 'N/A' }}
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
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
                <p>Are you sure you want to delete <strong>{{ $product->Name }}</strong>?</p>
                <ul class="text-muted small">
                    <li>This will permanently remove the product from your inventory</li>
                    <li>All related inventory records will be affected</li>
                    @if($product->orderItems->count() > 0)
                        <li class="text-danger">This product has {{ $product->orderItems->count() }} order item(s) associated with it</li>
                    @endif
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('staff.products.destroy', $product->ProductID) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection