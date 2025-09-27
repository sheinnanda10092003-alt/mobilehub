@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Inventory Report</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('staff.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Inventory Report</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('staff.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('staff.reports.export', ['type' => 'inventory']) }}" class="btn btn-outline-success">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
    </div>
</div>

<!-- Stock Status Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="stock_status" class="form-label">Stock Status Filter</label>
                <select class="form-select" id="stock_status" name="stock_status">
                    <option value="" {{ request('stock_status') == '' ? 'selected' : '' }}>All Products</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock (< 10)</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <a href="{{ route('staff.reports.inventory') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Summary -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-boxes fa-2x text-primary mb-3"></i>
                <h4 class="text-primary">{{ $inventorySummary['total_products'] }}</h4>
                <p class="card-text">Total Products</p>
                <small class="text-muted">All products</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-danger">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                <h4 class="text-danger">{{ $inventorySummary['out_of_stock'] }}</h4>
                <p class="card-text">Out of Stock</p>
                <small class="text-muted">0 units</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-exclamation-circle fa-2x text-warning mb-3"></i>
                <h4 class="text-warning">{{ $inventorySummary['low_stock'] }}</h4>
                <p class="card-text">Low Stock</p>
                <small class="text-muted">< 10 units</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-success mb-3"></i>
                <h4 class="text-success">${{ number_format($inventorySummary['total_inventory_value'], 2) }}</h4>
                <p class="card-text">Inventory Value</p>
                <small class="text-muted">Total worth</small>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Alerts -->
@if($inventorySummary['out_of_stock'] > 0 || $inventorySummary['low_stock'] > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle me-3"></i>
            <div>
                <strong>Inventory Alert:</strong>
                @if($inventorySummary['out_of_stock'] > 0)
                    {{ $inventorySummary['out_of_stock'] }} product(s) are out of stock.
                @endif
                @if($inventorySummary['low_stock'] > 0)
                    {{ $inventorySummary['low_stock'] }} product(s) have low stock levels.
                @endif
                Consider restocking soon to avoid sales disruption.
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Stock Level Distribution Chart -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Stock Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="stockChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-fire me-2"></i>Product Performance
                </h5>
            </div>
            <div class="card-body p-0">
                @if($products->where('order_items_count', '>', 0)->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Value</th>
                                    <th>Orders</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products->sortByDesc('order_items_count')->take(10) as $product)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $product->Name }}</strong>
                                            @if($product->Description)
                                                <br><small class="text-muted">{{ Str::limit($product->Description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $stockClass = 'success';
                                            $stockIcon = 'check-circle';
                                            if ($product->Stock == 0) {
                                                $stockClass = 'danger';
                                                $stockIcon = 'times-circle';
                                            } elseif ($product->Stock < 10) {
                                                $stockClass = 'warning';
                                                $stockIcon = 'exclamation-triangle';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $stockClass }}">
                                            <i class="fas fa-{{ $stockIcon }} me-1"></i>{{ $product->Stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($product->Price, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-success">${{ number_format($product->Price * $product->Stock, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->order_items_count }} sales</span>
                                    </td>
                                    <td>
                                        @php
                                            $maxOrders = $products->max('order_items_count');
                                            $performance = $maxOrders > 0 ? ($product->order_items_count / $maxOrders) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ $performance }}%"
                                                 title="{{ number_format($performance, 1) }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($performance, 1) }}%</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5>No Sales Data</h5>
                        <p class="text-muted">No product sales data available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Full Inventory Table -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-warehouse me-2"></i>Complete Inventory
            <span class="badge bg-primary">{{ $products->total() }} products</span>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Stock Level</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Sales Count</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="{{ $product->Stock == 0 ? 'table-danger' : ($product->Stock < 10 ? 'table-warning' : '') }}">
                            <td>
                                <strong>#{{ $product->ProductID }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->Name }}</strong>
                                    @if($product->image_url)
                                        <br><small class="text-muted">
                                            <i class="fas fa-image me-1"></i>Has image
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($product->Description)
                                    <small>{{ Str::limit($product->Description, 100) }}</small>
                                @else
                                    <span class="text-muted">No description</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stockClass = 'success';
                                    $stockText = 'In Stock';
                                    $stockIcon = 'check-circle';
                                    
                                    if ($product->Stock == 0) {
                                        $stockClass = 'danger';
                                        $stockText = 'Out of Stock';
                                        $stockIcon = 'times-circle';
                                    } elseif ($product->Stock < 10) {
                                        $stockClass = 'warning';
                                        $stockText = 'Low Stock';
                                        $stockIcon = 'exclamation-triangle';
                                    }
                                @endphp
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $stockClass }} me-2">
                                        <i class="fas fa-{{ $stockIcon }} me-1"></i>{{ $product->Stock }}
                                    </span>
                                    <small class="text-{{ $stockClass }}">{{ $stockText }}</small>
                                </div>
                            </td>
                            <td>
                                <strong>${{ number_format($product->Price, 2) }}</strong>
                            </td>
                            <td>
                                <span class="text-success font-weight-bold">
                                    ${{ number_format($product->Price * $product->Stock, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->order_items_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if($product->Stock == 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-ban me-1"></i>Out of Stock
                                    </span>
                                @elseif($product->Stock < 10)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Available
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                </div>
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <h5>No Products Found</h5>
                <p class="text-muted">No products match the selected filter criteria.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stock Distribution Pie Chart
    const ctx = document.getElementById('stockChart').getContext('2d');
    
    const stockData = {
        inStock: {{ $inventorySummary['total_products'] - $inventorySummary['out_of_stock'] - $inventorySummary['low_stock'] }},
        lowStock: {{ $inventorySummary['low_stock'] }},
        outOfStock: {{ $inventorySummary['out_of_stock'] }}
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: [stockData.inStock, stockData.lowStock, stockData.outOfStock],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
    }
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
@endpush