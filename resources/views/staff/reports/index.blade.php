@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Reports Dashboard</h1>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
            <i class="fas fa-download me-2"></i>Export Reports
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('staff.reports.export', ['type' => 'sales']) }}">Sales Report</a></li>
            <li><a class="dropdown-item" href="{{ route('staff.reports.export', ['type' => 'inventory']) }}">Inventory Report</a></li>
            <li><a class="dropdown-item" href="{{ route('staff.reports.export', ['type' => 'customers']) }}">Customer Report</a></li>
        </ul>
    </div>
</div>

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x text-primary mb-3"></i>
                <h4 class="text-primary">${{ number_format($stats['total_revenue'], 2) }}</h4>
                <p class="card-text">Total Revenue</p>
                <small class="text-muted">{{ $stats['total_orders'] }} orders</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-calendar-day fa-2x text-success mb-3"></i>
                <h4 class="text-success">${{ number_format($stats['revenue_today'], 2) }}</h4>
                <p class="card-text">Today's Revenue</p>
                <small class="text-muted">{{ $stats['orders_today'] }} orders</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-info mb-3"></i>
                <h4 class="text-info">{{ $stats['total_customers'] }}</h4>
                <p class="card-text">Total Customers</p>
                <small class="text-muted">Active users</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                <h4 class="text-warning">{{ $stats['low_stock_products'] }}</h4>
                <p class="card-text">Low Stock Items</p>
                <small class="text-muted">Need attention</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Report Links -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Sales Reports</h5>
                <p class="card-text">View sales performance, revenue trends, and order analytics.</p>
                <a href="{{ route('staff.reports.sales') }}" class="btn btn-primary">
                    View Sales Report
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-boxes fa-3x text-success mb-3"></i>
                <h5 class="card-title">Inventory Reports</h5>
                <p class="card-text">Monitor stock levels, product performance, and inventory value.</p>
                <a href="{{ route('staff.reports.inventory') }}" class="btn btn-success">
                    View Inventory Report
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-friends fa-3x text-info mb-3"></i>
                <h5 class="card-title">Customer Analytics</h5>
                <p class="card-text">Analyze customer behavior, spending patterns, and demographics.</p>
                <a href="{{ route('staff.reports.customers') }}" class="btn btn-info">
                    View Customer Analytics
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-mobile-alt fa-3x text-secondary mb-3"></i>
                <h5 class="card-title">Product Performance</h5>
                <p class="card-text">Track best-selling products and identify trending items.</p>
                <a href="{{ route('staff.reports.products') }}" class="btn btn-secondary">
                    View Product Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales Chart -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-area me-2"></i>Sales Trend (Last 30 Days)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>Top Selling Products
                </h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topProducts as $index => $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $product->Name }}</h6>
                                <small class="text-muted">${{ number_format($product->Price, 2) }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary rounded-pill">{{ $product->total_sold }} sold</span>
                                <div class="text-muted small">#{{ $index + 1 }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-mobile-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No sales data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($recentSales);
    
    const labels = salesData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    const revenues = salesData.map(item => parseFloat(item.revenue));
    const orderCounts = salesData.map(item => parseInt(item.orders));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue ($)',
                data: revenues,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Orders',
                data: orderCounts,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1,
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
                        text: 'Date'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
});
</script>
@endpush