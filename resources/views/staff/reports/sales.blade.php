@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Sales Report</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('staff.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Sales Report</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('staff.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('staff.reports.export', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-success">
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
                <a href="{{ route('staff.reports.sales') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Sales Summary -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-dollar-sign fa-2x text-primary mb-3"></i>
                <h4 class="text-primary">${{ number_format($salesSummary['total_revenue'], 2) }}</h4>
                <p class="card-text">Total Revenue</p>
                <small class="text-muted">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x text-success mb-3"></i>
                <h4 class="text-success">{{ $salesSummary['total_orders'] }}</h4>
                <p class="card-text">Total Orders</p>
                <small class="text-muted">Completed orders</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="fas fa-chart-line fa-2x text-info mb-3"></i>
                <h4 class="text-info">${{ number_format($salesSummary['average_order_value'], 2) }}</h4>
                <p class="card-text">Average Order Value</p>
                <small class="text-muted">Per order</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-calendar-day fa-2x text-warning mb-3"></i>
                <h4 class="text-warning">${{ number_format($salesSummary['total_revenue'] / max($startDate->diffInDays($endDate), 1), 2) }}</h4>
                <p class="card-text">Daily Average</p>
                <small class="text-muted">Revenue per day</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daily Sales Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-area me-2"></i>Daily Sales Trend
                </h5>
            </div>
            <div class="card-body">
                @if($dailySales->count() > 0)
                    <canvas id="dailySalesChart" height="100"></canvas>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-area fa-3x text-muted mb-3"></i>
                        <h5>No Sales Data</h5>
                        <p class="text-muted">No sales data available for the selected period.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sales Summary Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Sales Insights
                </h5>
            </div>
            <div class="card-body">
                @if($dailySales->count() > 0)
                    @php
                        $bestDay = $dailySales->sortByDesc('revenue')->first();
                        $worstDay = $dailySales->sortBy('revenue')->first();
                        $totalDays = $dailySales->count();
                        $daysWithSales = $dailySales->where('orders', '>', 0)->count();
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Best Sales Day</span>
                            <strong class="text-success">${{ number_format($bestDay->revenue, 2) }}</strong>
                        </div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($bestDay->date)->format('M d, Y') }}</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Active Sales Days</span>
                            <strong class="text-info">{{ $daysWithSales }}/{{ $totalDays }}</strong>
                        </div>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-info" 
                                 style="width: {{ ($daysWithSales / max($totalDays, 1)) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Period Growth</span>
                            @php
                                $firstHalf = $dailySales->take(ceil($dailySales->count() / 2))->sum('revenue');
                                $secondHalf = $dailySales->skip(ceil($dailySales->count() / 2))->sum('revenue');
                                $growth = $firstHalf > 0 ? (($secondHalf - $firstHalf) / $firstHalf) * 100 : 0;
                            @endphp
                            <strong class="{{ $growth >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                            </strong>
                        </div>
                        <small class="text-muted">Period over period</small>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No insights available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>Sales Details
            <span class="badge bg-primary">{{ $salesData->total() }} orders</span>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($salesData->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesData as $order)
                        <tr>
                            <td>
                                <strong>#{{ $order->OrderID }}</strong>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</small>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($order->OrderDate)->format('H:i A') }}</small>
                            </td>
                            <td>
                                @if($order->customer)
                                    <div>
                                        <strong>{{ $order->customer->FirstName }} {{ $order->customer->LastName }}</strong>
                                        <br><small class="text-muted">{{ $order->customer->Email }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Guest</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $order->orderItems->count() }} items</span>
                                @if($order->orderItems->count() > 0)
                                    <br><small class="text-muted">{{ $order->orderItems->sum('Quantity') }} units</small>
                                @endif
                            </td>
                            <td>
                                <strong class="text-success">${{ number_format($order->TotalAmount, 2) }}</strong>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($order->Status) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($order->Status) }}</span>
                            </td>
                            <td>
                                @if($order->payment)
                                    @php
                                        $paymentClass = match($order->payment->PaymentStatus) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $paymentClass }}">{{ ucfirst($order->payment->PaymentStatus) }}</span>
                                    <br><small class="text-muted">{{ ucfirst($order->payment->PaymentMethod) }}</small>
                                @else
                                    <span class="badge bg-secondary">No Payment</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('staff.orders.show', $order->OrderID) }}" 
                                   class="btn btn-outline-primary btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing {{ $salesData->firstItem() }} to {{ $salesData->lastItem() }} of {{ $salesData->total() }} orders
                </div>
                {{ $salesData->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5>No Sales Data</h5>
                <p class="text-muted">No sales found for the selected date range.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($dailySales->count() > 0)
    // Daily Sales Chart
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesData = @json($dailySales);
    
    const labels = dailySalesData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    const revenues = dailySalesData.map(item => parseFloat(item.revenue));
    const orderCounts = dailySalesData.map(item => parseInt(item.orders));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue ($)',
                data: revenues,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.3,
                fill: true,
                yAxisID: 'y'
            }, {
                label: 'Orders',
                data: orderCounts,
                borderColor: 'rgb(255, 99, 132)',
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
                        text: 'Orders'
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
</style>
@endpush