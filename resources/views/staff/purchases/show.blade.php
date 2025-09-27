@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Purchase Order #{{ $purchase->PurchaseID }}</h1>
    <div>
        @if($purchase->isPending())
            <form method="POST" action="{{ route('staff.purchases.receive', $purchase->PurchaseID) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success me-2" 
                        onclick="return confirm('Mark this purchase as received? This will update stock levels.')">
                    <i class="fas fa-check me-2"></i>Mark as Received
                </button>
            </form>
            
            <a href="{{ route('staff.purchases.edit', $purchase->PurchaseID) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            
            <form method="POST" action="{{ route('staff.purchases.cancel', $purchase->PurchaseID) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-warning me-2" 
                        onclick="return confirm('Are you sure you want to cancel this purchase?')">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
            </form>
        @endif
        
        <a href="{{ route('staff.purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Purchases
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Purchase Information</h5>
                @if($purchase->Status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                @elseif($purchase->Status === 'received')
                    <span class="badge bg-success">Received</span>
                @else
                    <span class="badge bg-secondary">Cancelled</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Supplier Information</h6>
                        <p class="mb-1"><strong>{{ $purchase->supplier->Name }}</strong></p>
                        <p class="mb-1">Contact: {{ $purchase->supplier->Contact }}</p>
                        <p class="mb-3">Address: {{ $purchase->supplier->Address }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Purchase Details</h6>
                        <p class="mb-1"><strong>Purchase Date:</strong> {{ $purchase->PurchaseDate->format('M d, Y') }}</p>
                        <p class="mb-1"><strong>Created By:</strong> {{ $purchase->creator->name ?? 'Unknown' }}</p>
                        <p class="mb-1"><strong>Created At:</strong> {{ $purchase->created_at->format('M d, Y g:i A') }}</p>
                        @if($purchase->updated_at != $purchase->created_at)
                            <p class="mb-1"><strong>Last Updated:</strong> {{ $purchase->updated_at->format('M d, Y g:i A') }}</p>
                        @endif
                    </div>
                </div>
                
                @if($purchase->Notes)
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Notes</h6>
                        <p class="mb-0">{{ $purchase->Notes }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Purchase Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $item->product->Name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->product->Description }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->product->Stock }} in stock</span>
                                </td>
                                <td>
                                    <strong>{{ $item->Quantity }}</strong>
                                </td>
                                <td>
                                    ${{ number_format($item->UnitPrice, 2) }}
                                </td>
                                <td>
                                    <strong>${{ number_format($item->TotalPrice, 2) }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th colspan="4" class="text-end">Total:</th>
                                <th>${{ number_format($purchase->TotalAmount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Items:</span>
                    <span><strong>{{ $purchase->items->count() }}</strong></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Quantity:</span>
                    <span><strong>{{ $purchase->items->sum('Quantity') }}</strong></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span><strong>Total Amount:</strong></span>
                    <span><strong class="text-success">${{ number_format($purchase->TotalAmount, 2) }}</strong></span>
                </div>
                
                @if($purchase->isPending())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Pending:</strong> This purchase is awaiting receipt. Stock levels will be updated when marked as received.
                    </div>
                @elseif($purchase->isReceived())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Received:</strong> This purchase has been received and stock levels have been updated.
                    </div>
                @else
                    <div class="alert alert-secondary">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Cancelled:</strong> This purchase order has been cancelled.
                    </div>
                @endif
            </div>
        </div>
        
        @if($purchase->isReceived())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Stock Impact</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">Stock levels that were updated when this purchase was received:</small>
                    <div class="mt-3">
                        @foreach($purchase->items as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">{{ $item->product->Name }}:</span>
                                <span class="badge bg-success">+{{ $item->Quantity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if($purchase->isPending())
    <!-- Quick Action Buttons -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <form method="POST" action="{{ route('staff.purchases.receive', $purchase->PurchaseID) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100" 
                                onclick="return confirm('Mark this purchase as received? This will update stock levels.')">
                            <i class="fas fa-check mb-2 d-block"></i>
                            Mark as Received
                            <small class="d-block">Update stock levels</small>
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('staff.purchases.edit', $purchase->PurchaseID) }}" class="btn btn-primary w-100">
                        <i class="fas fa-edit mb-2 d-block"></i>
                        Edit Purchase
                        <small class="d-block">Modify items or details</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <form method="POST" action="{{ route('staff.purchases.cancel', $purchase->PurchaseID) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning w-100" 
                                onclick="return confirm('Are you sure you want to cancel this purchase?')">
                            <i class="fas fa-times mb-2 d-block"></i>
                            Cancel Purchase
                            <small class="d-block">Mark as cancelled</small>
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form method="POST" action="{{ route('staff.purchases.destroy', $purchase->PurchaseID) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('Are you sure you want to delete this purchase? This action cannot be undone.')">
                            <i class="fas fa-trash mb-2 d-block"></i>
                            Delete Purchase
                            <small class="d-block">Remove permanently</small>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection