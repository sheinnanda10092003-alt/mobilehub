@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Product Performance Report</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('staff.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Product Performance</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('staff.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('staff.reports.export', ['type' => 'products', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-success">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('staff.reports.products') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Performance Summary -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-mobile-alt fa-2x text-primary mb-3"></i>
                <h4 class="text-primary">{{ $productPerformance->total() }}</h4>
                <p class="card-text">Total Products</p>
                <small class="text-muted">In catalog</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-fire fa-2x text-success mb-3"></i>
                <h4 class="text-success">{{ $productPerformance->where('total_sold', '>', 0)->count() }}</h4>
                <p class="card-text">Products Sold</p>
                <small class="text-muted">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="fas fa-chart-line fa-2x text-info mb-3"></i>
                <h4 class="text-info">{{ $productPerformance->sum('total_sold') }}</h4>
                <p class="card-text">Total Units Sold</p>
                <small class="text-muted">All products</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-warning mb-3"></i>
                <h4 class="text-warning">${{ number_format($productPerformance->sum('revenue'), 2) }}</h4>
                <p class="card-text">Total Revenue</p>
                <small class="text-muted">Product sales</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Performers Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>Top 10 Best Selling Products
                </h5>
            </div>
            <div class="card-body">
                @php
                    $topProducts = $productPerformance->sortByDesc('total_sold')->take(10);
                @endphp
                @if($topProducts->count() > 0)
                    <canvas id="topProductsChart" height="100"></canvas>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5>No Sales Data</h5>
                        <p class="text-muted">No product sales found for the selected period.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Performance Insights
                </h5>
            </div>
            <div class="card-body">
                @if($productPerformance->count() > 0)
                    @php
                        $soldProducts = $productPerformance->where('total_sold', '>', 0);
                        $topProduct = $soldProducts->sortByDesc('total_sold')->first();
                        $topRevenueProduct = $soldProducts->sortByDesc('revenue')->first();
                        $avgUnitsPerProduct = $soldProducts->count() > 0 ? $soldProducts->avg('total_sold') : 0;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Best Seller</span>
                        </div>
                        @if($topProduct)
                            <strong class="text-success">{{ $topProduct->Name }}</strong>
                            <br><small class="text-muted">{{ $topProduct->total_sold }} units sold</small>
                        @else
                            <span class="text-muted">No sales data</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Top Revenue Product</span>
                        </div>
                        @if($topRevenueProduct)
                            <strong class="text-info">{{ $topRevenueProduct->Name }}</strong>
                            <br><small class="text-muted">${{ number_format($topRevenueProduct->revenue, 2) }} revenue</small>
                        @else
                            <span class="text-muted">No revenue data</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Sales Conversion</span>
                            <strong class="text-warning">
                                {{ number_format(($soldProducts->count() / max($productPerformance->count(), 1)) * 100, 1) }}%
                            </strong>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-warning" 
                                 style="width: {{ ($soldProducts->count() / max($productPerformance->count(), 1)) * 100 }}%"></div>
                        </div>
                        <small class="text-muted">Products with sales</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Avg Units/Product</span>
                            <strong class="text-primary">{{ number_format($avgUnitsPerProduct, 1) }}</strong>
                        </div>
                        <small class="text-muted">For sold products</small>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No insights available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('staff.products.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-2"></i>Add New Product
                    </a>
                    <a href="{{ route('staff.products.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-boxes me-2"></i>Manage Products
                    </a>
                    <a href="{{ route('staff.reports.inventory') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-warehouse me-2"></i>View Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Performance Table -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>Product Performance Details
            <span class="badge bg-primary">{{ $productPerformance->total() }} products</span>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($productPerformance->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Price</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                            <th>Performance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $maxSold = $productPerformance->max('total_sold');
                        @endphp
                        @foreach($productPerformance->sortByDesc('total_sold') as $index => $product)
                        <tr class="{{ $product->total_sold > 0 ? '' : 'table-light' }}">
                            <td>
                                @if($product->total_sold > 0)
                                    <span class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }}">
                                        #{{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->Name }}</strong>
                                    <br><small class="text-muted">ID: {{ $product->ProductID }}</small>
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
                                @if($product->total_sold > 0)
                                    <span class="badge bg-primary">{{ $product->total_sold }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($product->revenue > 0)
                                    <strong class="text-success">${{ number_format($product->revenue, 2) }}</strong>
                                @else
                                    <span class="text-muted">$0.00</span>
                                @endif
                            </td>
                            <td>
                                @if($maxSold > 0 && $product->total_sold > 0)
                                    @php
                                        $performance = ($product->total_sold / $maxSold) * 100;
                                    @endphp
                                    <div class="progress" style="height: 8px; min-width: 80px;">
                                        <div class="progress-bar bg-info" 
                                             style="width: {{ $performance }}%"
                                             title="{{ number_format($performance, 1) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($performance, 1) }}%</small>
                                @else
                                    <span class="text-muted">No sales</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('staff.products.show', $product->ProductID) }}" 
                                       class="btn btn-outline-primary btn-sm" title="View Product">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('staff.products.edit', $product->ProductID) }}" 
                                       class="btn btn-outline-secondary btn-sm" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing {{ $productPerformance->firstItem() }} to {{ $productPerformance->lastItem() }} of {{ $productPerformance->total() }} products
                </div>
                {{ $productPerformance->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                <h5>No Products Found</h5>
                <p class="text-muted">No products found for the selected date range.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @php
        $topProducts = $productPerformance->sortByDesc('total_sold')->take(10);
    @endphp
    @if($topProducts->count() > 0)
    // Top Products Chart
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsData = @json($topProducts->values()->all());
    
    const labels = topProductsData.map(product => product.Name.length > 20 ? product.Name.substring(0, 20) + '...' : product.Name);
    const soldData = topProductsData.map(product => product.total_sold);
    const revenueData = topProductsData.map(product => parseFloat(product.revenue));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Units Sold',
                data: soldData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Revenue ($)',
                data: revenueData,
                type: 'line',
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.3,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Products'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Units Sold'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
    @endif
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
    .table-light {
        background-color: rgba(248, 249, 250, 0.5) !important;
    }
</style>
@endpush