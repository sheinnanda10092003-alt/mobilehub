<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Get basic statistics
        $stats = $this->getBasicStats();
        
        // Get recent sales data for chart
        $recentSales = $this->getRecentSalesData();
        
        // Get top selling products
        $topProducts = $this->getTopSellingProducts();
        
        return view('staff.reports.index', compact('stats', 'recentSales', 'topProducts'));
    }

    /**
     * Sales report.
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        $salesData = Order::whereBetween('OrderDate', [$startDate, $endDate])
                         ->where('Status', '!=', 'cancelled')
                         ->with(['orderItems.product', 'customer', 'payment'])
                         ->orderBy('OrderDate', 'desc')
                         ->paginate(20);
        
        $salesSummary = [
            'total_revenue' => $salesData->sum('TotalAmount'),
            'total_orders' => $salesData->count(),
            'average_order_value' => $salesData->avg('TotalAmount'),
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
        
        // Daily sales breakdown
        $dailySales = Order::whereBetween('OrderDate', [$startDate, $endDate])
                          ->where('Status', '!=', 'cancelled')
                          ->selectRaw('DATE(OrderDate) as date, COUNT(*) as orders, SUM(TotalAmount) as revenue')
                          ->groupBy('date')
                          ->orderBy('date')
                          ->get();
        
        return view('staff.reports.sales', compact('salesData', 'salesSummary', 'dailySales', 'startDate', 'endDate'));
    }

    /**
     * Inventory report.
     */
    public function inventory(Request $request)
    {
        $query = Product::query();
        
        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->where('Stock', '<', 10);
                    break;
                case 'out':
                    $query->where('Stock', '=', 0);
                    break;
                case 'in_stock':
                    $query->where('Stock', '>', 0);
                    break;
            }
        }
        
        $products = $query->withCount(['orderItems' => function ($query) {
                            $query->whereHas('order', function ($q) {
                                $q->where('Status', '!=', 'cancelled');
                            });
                        }])
                        ->orderBy('Stock', 'asc')
                        ->paginate(20);
        
        // Inventory summary
        $inventorySummary = [
            'total_products' => Product::count(),
            'out_of_stock' => Product::where('Stock', 0)->count(),
            'low_stock' => Product::where('Stock', '<', 10)->where('Stock', '>', 0)->count(),
            'total_inventory_value' => Product::selectRaw('SUM(Price * Stock) as total')->first()->total ?? 0,
        ];
        
        return view('staff.reports.inventory', compact('products', 'inventorySummary'));
    }

    /**
     * Customer analytics report.
     */
    public function customers(Request $request)
    {
        // Customer statistics
        $customerStats = [
            'total_customers' => Customer::count(),
            'new_customers_this_month' => Customer::whereMonth('created_at', Carbon::now()->month)->count(),
            'customers_with_orders' => Customer::has('orders')->count(),
            'average_orders_per_customer' => round(Order::count() / max(Customer::count(), 1), 2),
        ];
        
        // Top customers by spending
        $topCustomers = Customer::leftJoin('orders', 'customers.CustomerID', '=', 'orders.CustomerID')
                               ->where('orders.Status', '!=', 'cancelled')
                               ->select('customers.CustomerID', 'customers.Name', 'customers.Email', 'customers.created_at', 'customers.updated_at')
                               ->selectRaw('COALESCE(SUM(orders.TotalAmount), 0) as total_spent')
                               ->selectRaw('COUNT(orders.OrderID) as order_count')
                               ->groupBy('customers.CustomerID', 'customers.Name', 'customers.Email', 'customers.created_at', 'customers.updated_at')
                               ->orderBy('total_spent', 'desc')
                               ->limit(10)
                               ->get();
        
        // Customer registration trends (last 12 months)
        $registrationTrends = Customer::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                                    ->where('created_at', '>=', Carbon::now()->subMonths(12))
                                    ->groupBy('year', 'month')
                                    ->orderBy('year')
                                    ->orderBy('month')
                                    ->get();
        
        return view('staff.reports.customers', compact('customerStats', 'topCustomers', 'registrationTrends'));
    }

    /**
     * Product performance report.
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        // Product sales performance
        $productPerformance = Product::leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
                                   ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                                       $join->on('order_items.OrderID', '=', 'orders.OrderID')
                                            ->whereBetween('orders.OrderDate', [$startDate, $endDate])
                                            ->where('orders.Status', '!=', 'cancelled');
                                   })
                                   ->select('products.ProductID', 'products.Name', 'products.Description', 'products.Price', 'products.Stock', 'products.image_url')
                                   ->selectRaw('COALESCE(SUM(order_items.Quantity), 0) as total_sold')
                                   ->selectRaw('COALESCE(SUM(order_items.Quantity * order_items.UnitPrice), 0) as revenue')
                                   ->groupBy('products.ProductID', 'products.Name', 'products.Description', 'products.Price', 'products.Stock', 'products.image_url')
                                   ->orderBy('total_sold', 'desc')
                                   ->paginate(20);
        
        return view('staff.reports.products', compact('productPerformance', 'startDate', 'endDate'));
    }

    /**
     * Export reports data.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        $filename = $type . '_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        switch ($type) {
            case 'sales':
                return $this->exportSalesReport($startDate, $endDate, $headers);
            case 'inventory':
                return $this->exportInventoryReport($headers);
            case 'customers':
                return $this->exportCustomersReport($headers);
            default:
                return redirect()->back()->with('error', 'Invalid export type.');
        }
    }

    /**
     * Get basic statistics for dashboard.
     */
    private function getBasicStats()
    {
        return [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('Status', '!=', 'cancelled')->sum('TotalAmount'),
            'total_customers' => Customer::count(),
            'total_products' => Product::count(),
            'orders_today' => Order::whereDate('OrderDate', Carbon::today())->count(),
            'revenue_today' => Order::whereDate('OrderDate', Carbon::today())
                                  ->where('Status', '!=', 'cancelled')
                                  ->sum('TotalAmount'),
            'low_stock_products' => Product::where('Stock', '<', 10)->count(),
        ];
    }

    /**
     * Get recent sales data for charts.
     */
    private function getRecentSalesData()
    {
        return Order::selectRaw('DATE(OrderDate) as date, COUNT(*) as orders, SUM(TotalAmount) as revenue')
                   ->where('OrderDate', '>=', Carbon::now()->subDays(30))
                   ->where('Status', '!=', 'cancelled')
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }

    /**
     * Get top selling products.
     */
    private function getTopSellingProducts()
    {
        return Product::leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
                     ->leftJoin('orders', function ($join) {
                         $join->on('order_items.OrderID', '=', 'orders.OrderID')
                              ->where('orders.Status', '!=', 'cancelled');
                     })
                     ->select('products.Name', 'products.Price')
                     ->selectRaw('COALESCE(SUM(order_items.Quantity), 0) as total_sold')
                     ->groupBy('products.ProductID', 'products.Name', 'products.Price')
                     ->orderBy('total_sold', 'desc')
                     ->limit(5)
                     ->get();
    }

    /**
     * Export sales report to CSV.
     */
    private function exportSalesReport($startDate, $endDate, $headers)
    {
        $orders = Order::whereBetween('OrderDate', [$startDate, $endDate])
                      ->where('Status', '!=', 'cancelled')
                      ->with(['customer'])
                      ->get();
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Order ID', 'Date', 'Customer', 'Total Amount', 'Status', 'Payment Status']);
            
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->OrderID,
                    $order->OrderDate,
                    $order->customer ? $order->customer->FirstName . ' ' . $order->customer->LastName : 'N/A',
                    $order->TotalAmount,
                    $order->Status,
                    $order->payment ? $order->payment->PaymentStatus : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export inventory report to CSV.
     */
    private function exportInventoryReport($headers)
    {
        $products = Product::all();
        
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Product ID', 'Product Name', 'Price', 'Stock', 'Description']);
            
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->ProductID,
                    $product->Name,
                    $product->Price,
                    $product->Stock,
                    $product->Description
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export customers report to CSV.
     */
    private function exportCustomersReport($headers)
    {
        $customers = Customer::leftJoin('orders', 'customers.CustomerID', '=', 'orders.CustomerID')
                           ->where('orders.Status', '!=', 'cancelled')
                           ->select('customers.*')
                           ->selectRaw('COALESCE(SUM(orders.TotalAmount), 0) as total_spent')
                           ->selectRaw('COUNT(orders.OrderID) as total_orders')
                           ->groupBy('customers.CustomerID')
                           ->get();
        
        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Customer ID', 'Name', 'Email', 'Phone', 'Total Orders', 'Total Spent', 'Registration Date']);
            
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->CustomerID,
                    $customer->FirstName . ' ' . $customer->LastName,
                    $customer->Email,
                    $customer->Phone,
                    $customer->total_orders ?: 0,
                    $customer->total_spent ?: 0,
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}