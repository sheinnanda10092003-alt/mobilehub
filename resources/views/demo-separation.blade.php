<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobileHub - Authentication Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .customer-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .staff-section {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            color: white;
        }
        .demo-card {
            transition: transform 0.2s;
        }
        .demo-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4"><i class="fas fa-mobile-alt"></i> MobileHub</h1>
            <p class="lead">Separate Authentication Systems Demo</p>
        </div>

        <div class="row">
            <!-- Customer Section -->
            <div class="col-md-6 mb-4">
                <div class="card demo-card h-100 border-0 shadow-lg">
                    <div class="card-header customer-section text-center py-4">
                        <h3><i class="fas fa-shopping-cart"></i> CUSTOMER AREA</h3>
                        <p class="mb-0">For Mobile Phone Buyers</p>
                    </div>
                    <div class="card-body p-4">
                        <h5>What Customers Can Do:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Browse mobile phones</li>
                            <li><i class="fas fa-check text-success"></i> View product details</li>
                            <li><i class="fas fa-check text-success"></i> Add items to cart</li>
                            <li><i class="fas fa-check text-success"></i> Place orders</li>
                            <li><i class="fas fa-check text-success"></i> Manage their profile</li>
                        </ul>
                        
                        <h6 class="mt-4">Customer Authentication URLs:</h6>
                        <div class="small bg-light p-3 rounded">
                            <strong>Login:</strong> <code>/customer/login</code><br>
                            <strong>Signup:</strong> <code>/customer/signup</code><br>
                            <strong>Home:</strong> <code>/</code>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('customer.login') }}" class="btn btn-primary me-2">
                            <i class="fas fa-sign-in-alt"></i> Customer Login
                        </a>
                        <a href="{{ route('customer.signup') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Customer Signup
                        </a>
                    </div>
                </div>
            </div>

            <!-- Staff Section -->
            <div class="col-md-6 mb-4">
                <div class="card demo-card h-100 border-0 shadow-lg">
                    <div class="card-header staff-section text-center py-4">
                        <h3><i class="fas fa-cog"></i> STAFF AREA</h3>
                        <p class="mb-0">For Store Administrators</p>
                    </div>
                    <div class="card-body p-4">
                        <h5>What Staff Can Do:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Manage products (CRUD)</li>
                            <li><i class="fas fa-check text-success"></i> View inventory</li>
                            <li><i class="fas fa-check text-success"></i> Manage suppliers</li>
                            <li><i class="fas fa-check text-success"></i> View orders</li>
                            <li><i class="fas fa-check text-success"></i> Access dashboard</li>
                        </ul>
                        
                        <h6 class="mt-4">Staff Authentication URLs:</h6>
                        <div class="small bg-light p-3 rounded">
                            <strong>Login:</strong> <code>/staff/login</code><br>
                            <strong>Signup:</strong> <code>/staff/signup</code><br>
                            <strong>Dashboard:</strong> <code>/staff/dashboard</code>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('staff.login') }}" class="btn btn-dark me-2">
                            <i class="fas fa-sign-in-alt"></i> Staff Login
                        </a>
                        <a href="{{ route('staff.signup') }}" class="btn btn-warning">
                            <i class="fas fa-user-plus"></i> Staff Signup
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Differences -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-info text-white">
                        <h4><i class="fas fa-info-circle"></i> Key Differences</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-shopping-cart text-primary"></i> Customer System</h5>
                                <ul>
                                    <li><strong>Layout:</strong> Customer-focused with shopping features</li>
                                    <li><strong>Colors:</strong> Blue/Purple gradient theme</li>
                                    <li><strong>Features:</strong> Shopping cart, product browsing</li>
                                    <li><strong>Auth Guard:</strong> <code>web</code></li>
                                    <li><strong>Database:</strong> <code>customers</code> table</li>
                                    <li><strong>Fields:</strong> Name, Email, Password</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-cog text-secondary"></i> Staff System</h5>
                                <ul>
                                    <li><strong>Layout:</strong> Administrative panel design</li>
                                    <li><strong>Colors:</strong> Dark theme with warning accents</li>
                                    <li><strong>Features:</strong> Product management, dashboard</li>
                                    <li><strong>Auth Guard:</strong> <code>staff</code></li>
                                    <li><strong>Database:</strong> <code>staff</code> table</li>
                                    <li><strong>Fields:</strong> UserName, StaffName, Password</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Links -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-link"></i> Quick Test Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6>Customer Area</h6>
                                <a href="{{ route('home') }}" class="btn btn-primary btn-sm mb-2 d-block">
                                    <i class="fas fa-home"></i> Customer Homepage
                                </a>
                                <a href="{{ route('customer.login') }}" class="btn btn-outline-primary btn-sm mb-2 d-block">
                                    <i class="fas fa-sign-in-alt"></i> Customer Login
                                </a>
                            </div>
                            <div class="col-md-4">
                                <h6>Staff Area</h6>
                                <a href="{{ route('staff.login') }}" class="btn btn-dark btn-sm mb-2 d-block">
                                    <i class="fas fa-sign-in-alt"></i> Staff Login
                                </a>
                                <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-dark btn-sm mb-2 d-block">
                                    <i class="fas fa-tachometer-alt"></i> Staff Dashboard
                                </a>
                            </div>
                            <div class="col-md-4">
                                <h6>Management</h6>
                                <a href="{{ route('staff.products.index') }}" class="btn btn-warning btn-sm mb-2 d-block">
                                    <i class="fas fa-mobile-alt"></i> Manage Products
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>