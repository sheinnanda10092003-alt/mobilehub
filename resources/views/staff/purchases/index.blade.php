@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Purchase Management</h1>
    <a href="{{ route('staff.purchases.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Purchase Order
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Purchase Orders</h5>
        <div>
            <span class="badge bg-warning me-2">{{ $purchases->where('Status', 'pending')->count() }} Pending</span>
            <span class="badge bg-success me-2">{{ $purchases->where('Status', 'received')->count() }} Received</span>
            <span class="badge bg-secondary">{{ $purchases->where('Status', 'cancelled')->count() }} Cancelled</span>
        </div>
    </div>
    <div class="card-body">
        @if($purchases->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supplier</th>
                            <th>Purchase Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                        <tr>
                            <td>
                                <strong>#{{ $purchase->PurchaseID }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $purchase->supplier->Name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $purchase->supplier->Contact }}</small>
                                </div>
                            </td>
                            <td>{{ $purchase->PurchaseDate->format('M d, Y') }}</td>
                            <td>
                                <strong>${{ number_format($purchase->TotalAmount, 2) }}</strong>
                            </td>
                            <td>
                                @if($purchase->Status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($purchase->Status === 'received')
                                    <span class="badge bg-success">Received</span>
                                @else
                                    <span class="badge bg-secondary">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                {{ $purchase->creator->name ?? 'Unknown' }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $purchase->items->count() }} items</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('staff.purchases.show', $purchase->PurchaseID) }}" 
                                       class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($purchase->isPending())
                                        <a href="{{ route('staff.purchases.edit', $purchase->PurchaseID) }}" 
                                           class="btn btn-outline-secondary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                onclick="markAsReceived({{ $purchase->PurchaseID }})" title="Mark as Received">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                onclick="cancelPurchase({{ $purchase->PurchaseID }})" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deletePurchase({{ $purchase->PurchaseID }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $purchases->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Purchase Orders Found</h5>
                <p class="text-muted">Create your first purchase order to get started.</p>
                <a href="{{ route('staff.purchases.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Purchase Order
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Hidden Forms for Actions -->
<form id="receiveForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<form id="cancelForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function markAsReceived(purchaseId) {
    if (confirm('Are you sure you want to mark this purchase as received? This will update the stock levels.')) {
        const form = document.getElementById('receiveForm');
        form.action = `/staff/purchases/${purchaseId}/receive`;
        form.submit();
    }
}

function cancelPurchase(purchaseId) {
    if (confirm('Are you sure you want to cancel this purchase order?')) {
        const form = document.getElementById('cancelForm');
        form.action = `/staff/purchases/${purchaseId}/cancel`;
        form.submit();
    }
}

function deletePurchase(purchaseId) {
    if (confirm('Are you sure you want to delete this purchase order? This action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/staff/purchases/${purchaseId}`;
        form.submit();
    }
}
</script>
@endpush