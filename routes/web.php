<?php

use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\CustomerController as StaffCustomerController;
use App\Http\Controllers\Staff\ReportController;
use App\Http\Controllers\Staff\ProductVariantController;
use App\Http\Controllers\Staff\PurchaseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// Demo route to show separation
Route::get('/demo', function () {
    return view('demo-separation');
})->name('demo');

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'app' => config('app.name')
    ]);
});

// Root route - Homepage for customers
Route::get('/', [CustomerController::class, 'homepage'])->name('home');

// Search suggestions API
Route::get('/api/search-suggestions', [CustomerController::class, 'searchSuggestions'])->name('search.suggestions');

// Customer Authentication Routes - Clearly separated
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [CustomerAuthController::class, 'login'])->name('login.post');
    Route::get('signup', [CustomerAuthController::class, 'signup'])->name('signup');
    Route::post('register', [CustomerAuthController::class, 'register'])->name('register');
    Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// Legacy customer routes (for backward compatibility)
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.post');

// Customer Protected Routes
Route::middleware('auth:web')->group(function () {
    // Order routes
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'customerOrders'])->name('customer.orders');
    Route::get('/orders/{id}', [OrderController::class, 'customerOrderShow'])->name('customer.orders.show');
    
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-variant', [CartController::class, 'addVariant'])->name('cart.add.variant');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    
    // AJAX cart routes
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
    
    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Staff Routes
Route::prefix('staff')->name('staff.')->group(function () {
    // Staff Authentication Routes
    Route::get('login', [StaffAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [StaffAuthController::class, 'login'])->name('login.post');
    Route::get('signup', [StaffAuthController::class, 'showSignup'])->name('signup');
    Route::post('signup', [StaffAuthController::class, 'signup'])->name('signup.post');
    Route::post('logout', [StaffAuthController::class, 'logout'])->name('logout');
    
    // Staff Protected Routes
    Route::middleware('auth:staff')->group(function () {
        Route::get('dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        
        // Product management
        Route::resource('products', ProductController::class);
        
        // Product variant management
        Route::get('products/{product}/variants', [ProductVariantController::class, 'index'])->name('products.variants.index');
        Route::get('products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('products.variants.create');
        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])->name('products.variants.store');
        Route::get('products/{product}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('products.variants.edit');
        Route::put('products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('products.variants.update');
        Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
        Route::patch('products/{product}/variants/{variant}/toggle', [ProductVariantController::class, 'toggle'])->name('products.variants.toggle');
        
        // Order management
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::put('orders/{id}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
        
        // Customer management
        Route::get('customers', [StaffCustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{id}', [StaffCustomerController::class, 'show'])->name('customers.show');
        Route::put('customers/{id}', [StaffCustomerController::class, 'update'])->name('customers.update');
        Route::get('customers/{id}/orders', [StaffCustomerController::class, 'getOrders'])->name('customers.orders');
        Route::get('customers/export', [StaffCustomerController::class, 'export'])->name('customers.export');
        
        // Reports management
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
        
        // Purchase management
        Route::resource('purchases', PurchaseController::class);
        Route::patch('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
        Route::patch('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    });
});
