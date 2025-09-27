<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create()
{
    $products = Product::where('Stock', '>', 0)->get();
    return view('orders.create', compact('products'));
}

public function store(Request $request)
{
    $request->validate([
        'products' => 'required|array',
        'products.*.id' => 'exists:products,ProductID',
        'products.*.quantity' => 'integer|min:1',
    ]);

    $customer = auth()->user();
    $total = 0;

    foreach ($request->products as $item) {
        $product = Product::find($item['id']);
        if ($product->Stock < $item['quantity']) {
            return back()->withErrors("Insufficient stock for {$product->Name}");
        }
        $total += $product->Price * $item['quantity'];
    }

    $order = Order::create([
        'CustomerID' => $customer->CustomerID,
        'OrderDate' => now(),
        'TotalAmount' => $total,
        'Status' => 'pending',
    ]);

    foreach ($request->products as $item) {
        OrderItem::create([
            'OrderID' => $order->OrderID,
            'ProductID' => $item['id'],
            'Quantity' => $item['quantity'],
            'UnitPrice' => Product::find($item['id'])->Price,
        ]);
        Product::find($item['id'])->decrement('Stock', $item['quantity']);
    }
    return redirect()->route('home')->with('success', 'Order placed successfully!');
}

    /**
     * Display orders for admin dashboard
     */
    public function index(Request $request)
    {
        // This should only be accessible by staff
        $query = Order::with(['customer', 'payment', 'orderItems.product'])
            ->orderBy('OrderDate', 'desc');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('Status', $request->status);
        }

        // Filter by payment status if provided
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }

        // Search by order ID or customer name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('OrderID', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_email', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('Name', 'like', "%{$search}%")
                                   ->orWhere('Email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15);
        $orderStatuses = Order::getOrderStatuses();
        $paymentStatuses = [
            Payment::STATUS_PENDING => 'Pending',
            Payment::STATUS_COMPLETED => 'Completed',
            Payment::STATUS_FAILED => 'Failed',
            Payment::STATUS_REFUNDED => 'Refunded'
        ];

        return view('staff.orders.index', compact('orders', 'orderStatuses', 'paymentStatuses'));
    }

    /**
     * Show order details
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'payment', 'orderItems.product'])
            ->findOrFail($id);

        return view('staff.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:' . implode(',', array_keys(Order::getOrderStatuses()))
            ]);

            $order = Order::findOrFail($id);
            $order->update(['Status' => $request->status]);

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order status updated successfully!',
                    'order_id' => $id,
                    'new_status' => $request->status
                ]);
            }

            return redirect()->back()->with('success', 'Order status updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status provided.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Order not found.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Order status update failed', [
                'order_id' => $id,
                'status' => $request->status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating order status. Please try again.',
                    'debug' => app()->environment('local') ? $e->getMessage() : null
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating order status. Please try again.');
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_status' => 'required|in:pending,completed,failed,refunded',
                'transaction_id' => 'nullable|string'
            ]);

            $order = Order::findOrFail($id);
            
            if (!$order->payment) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No payment record found for this order.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'No payment record found for this order.');
            }

            $updateData = [
                'payment_status' => $request->payment_status
            ];

            if ($request->payment_status === Payment::STATUS_COMPLETED && !$order->payment->paid_at) {
                $updateData['paid_at'] = now();
            }

            if ($request->transaction_id) {
                $updateData['transaction_id'] = $request->transaction_id;
            }

            $order->payment->update($updateData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment status updated successfully!',
                    'order_id' => $id,
                    'payment_status' => $request->payment_status
                ]);
            }

            return redirect()->back()->with('success', 'Payment status updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment status provided.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Order not found.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Payment status update failed', [
                'order_id' => $id,
                'payment_status' => $request->payment_status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating payment status. Please try again.',
                    'debug' => app()->environment('local') ? $e->getMessage() : null
                ], 500);
            }
            return redirect()->back()->with('error', 'Error updating payment status. Please try again.');
        }
    }

    /**
     * Show customer orders (for customer dashboard)
     */
    public function customerOrders()
    {
        $orders = Order::with(['payment', 'orderItems.product'])
            ->select('*')
            ->where('CustomerID', Auth::id())
            ->orderBy('OrderDate', 'desc')
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show customer order details
     */
    public function customerOrderShow($id)
    {
        $order = Order::with(['payment', 'orderItems.product'])
            ->select('*')
            ->where('OrderID', $id)
            ->where('CustomerID', Auth::id())
            ->firstOrFail();

        return view('customer.orders.show', compact('order'));
    }

}
