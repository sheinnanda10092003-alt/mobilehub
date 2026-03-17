<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'like', '%' . $search . '%')
                  ->orWhere('Email', 'like', '%' . $search . '%')
                  ->orWhere('Phone', 'like', '%' . $search . '%');
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $customers = $query->withCount('orders')
                          ->orderBy($sortBy, $sortDirection)
                          ->paginate(15);
        
        return view('staff.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = Customer::with(['orders.orderItems.product', 'orders.payment'])
                           ->findOrFail($id);
        
        $customerStats = [
            'total_orders' => $customer->orders->count(),
            'total_spent' => $customer->orders->where('Status', '!=', 'cancelled')->sum('TotalAmount'),
            'pending_orders' => $customer->orders->where('Status', 'pending')->count(),
            'completed_orders' => $customer->orders->where('Status', 'completed')->count(),
            'last_order_date' => $customer->orders->max('OrderDate'),
        ];
        
        return view('staff.customers.show', compact('customer', 'customerStats'));
    }

    /**
     * Update customer status or details.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Add status field to customer if it doesn't exist
        if ($request->has('status')) {
            $customer->status = $request->status;
        }
        
        if ($request->has('notes')) {
            $customer->admin_notes = $request->notes;
        }
        
        $customer->save();
        
        return redirect()->route('staff.customers.show', $id)
                        ->with('success', 'Customer updated successfully.');
    }

    /**
     * Get customer orders for AJAX.
     */
    public function getOrders($id)
    {
        $customer = Customer::findOrFail($id);
        $orders = $customer->orders()
                          ->with(['orderItems.product', 'payment'])
                          ->orderBy('OrderDate', 'desc')
                          ->paginate(10);
        
        return response()->json([
            'orders' => $orders,
            'customer' => $customer
        ]);
    }

    /**
     * Export customers to CSV.
     */
    public function export(Request $request)
    {
        $customers = Customer::withCount('orders')->get();
        
        $filename = 'customers_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Total Orders',
                'Registration Date'
            ]);
            
            // Add customer data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->CustomerID,
                    $customer->FirstName,
                    $customer->LastName,
                    $customer->Email,
                    $customer->Phone,
                    $customer->orders_count,
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}