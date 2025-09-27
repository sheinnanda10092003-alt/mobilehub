@extends('staff.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Customer Analytics</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('staff.reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Customer Analytics</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('staff.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('staff.reports.export', ['type' => 'customers']) }}" class="btn btn-outline-success">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
    </div>
</div>

<!-- Customer Statistics Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-primary mb-3"></i>
                <h4 class="text-primary">{{ $customerStats['total_customers'] }}</h4>
                <p class="card-text">Total Customers</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-user-plus fa-2x text-success mb-3"></i>
                <h4 class="text-success">{{ $customerStats['new_customers_this_month'] }}</h4>
                <p class="card-text">New This Month</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x text-info mb-3"></i>
                <h4 class="text-info">{{ $customerStats['customers_with_orders'] }}</h4>
                <p class="card-text">Active Customers</p>
                <small class="text-muted">With Orders</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-chart-bar fa-2x text-warning mb-3"></i>
                <h4 class="text-warning">{{ $customerStats['average_orders_per_customer'] }}</h4>
                <p class="card-text">Avg Orders/Customer</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Customers -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-crown me-2"></i>Top Customers by Spending
                </h5>
            </div>
            <div class="card-body p-0">
                @if($topCustomers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Total Orders</th>
                                    <th>Total Spent</th>
                                    <th>Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $index => $customer)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white rounded-circle me-3" 
                                                 style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                {{ substr($customer->FirstName, 0, 1) }}{{ substr($customer->LastName, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $customer->FirstName }} {{ $customer->LastName }}</strong>
                                                <br><small class="text-muted">ID: {{ $customer->CustomerID }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $customer->Email }}</small>
                                        @if($customer->Phone)
                                            <br><small class="text-muted">{{ $customer->Phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $customer->order_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($customer->total_spent ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $avgValue = ($customer->order_count ?? 0) > 0 ? ($customer->total_spent ?? 0) / ($customer->order_count ?? 1) : 0;
                                        @endphp
                                        <span class="text-muted">${{ number_format($avgValue, 2) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5>No Customer Data</h5>
                        <p class="text-muted">No customer spending data available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Registration Trends -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Registration Trends
                </h5>
            </div>
            <div class="card-body">
                @if($registrationTrends->count() > 0)
                    <canvas id="registrationChart" height="200"></canvas>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No registration data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Customer Insights -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Customer Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Customer Retention Rate</span>
                        <strong class="text-success">
                            {{ number_format(($customerStats['customers_with_orders'] / max($customerStats['total_customers'], 1)) * 100, 1) }}%
                        </strong>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ ($customerStats['customers_with_orders'] / max($customerStats['total_customers'], 1)) * 100 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>New Customer Growth</span>
                        <strong class="text-info">+{{ $customerStats['new_customers_this_month'] }}</strong>
                    </div>
                    <small class="text-muted">This month</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Average Order Frequency</span>
                        <strong class="text-warning">{{ $customerStats['average_orders_per_customer'] }}</strong>
                    </div>
                    <small class="text-muted">Orders per customer</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($registrationTrends->count() > 0)
    // Registration Trends Chart
    const ctx = document.getElementById('registrationChart').getContext('2d');
    const registrationData = @json($registrationTrends);
    
    const labels = registrationData.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                           'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1] + ' ' + item.year;
    });
    
    const counts = registrationData.map(item => item.count);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'New Registrations',
                data: counts,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    display: true
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
    .avatar {
        font-weight: 600;
    }
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        border-radius: 10px;
    }
</style>
@endpush