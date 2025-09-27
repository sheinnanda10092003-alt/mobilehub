<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Show checkout page
     */
    public function index()
    {
        $cartItems = Cart::getCartItemsForCustomer(Auth::id());
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Add some products before checkout.');
        }

        $total = Cart::getCartTotalForCustomer(Auth::id());
        $paymentMethods = Payment::getPaymentMethods();
        $customer = Auth::user();

        return view('customer.checkout.index', compact('cartItems', 'total', 'paymentMethods', 'customer'));
    }

    /**
     * Process checkout and create order
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'payment_method' => 'required|string|in:' . implode(',', array_keys(Payment::getPaymentMethods())),
            'notes' => 'nullable|string|max:1000',
        ]);

        $customerId = Auth::id();
        $cartItems = Cart::getCartItemsForCustomer($customerId);
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Check stock availability
        foreach ($cartItems as $cartItem) {
            if ($cartItem->quantity > $cartItem->product->Stock) {
                return back()->withErrors([
                    'stock' => "Not enough stock for {$cartItem->product->Name}. Only {$cartItem->product->Stock} available."
                ])->withInput();
            }
        }

        $total = Cart::getCartTotalForCustomer($customerId);

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'CustomerID' => $customerId,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country,
                'notes' => $request->notes,
                'OrderDate' => now(),
                'TotalAmount' => $total,
                'Status' => Order::STATUS_PENDING
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'OrderID' => $order->OrderID,
                    'ProductID' => $cartItem->ProductID,
                    'Quantity' => $cartItem->quantity,
                    'UnitPrice' => $cartItem->price
                ]);

                // Update product stock
                $product = Product::find($cartItem->ProductID);
                $product->decrement('Stock', $cartItem->quantity);
            }

            // Create payment record
            $payment = Payment::create([
                'OrderID' => $order->OrderID,
                'payment_method' => $request->payment_method,
                'amount' => $total,
                'payment_status' => Payment::STATUS_PENDING
            ]);

            // Handle different payment methods
            switch ($request->payment_method) {
                case Payment::METHOD_CASH_ON_DELIVERY:
                    // COD orders stay pending until delivered
                    break;
                    
                case Payment::METHOD_CREDIT_CARD:
                case Payment::METHOD_DEBIT_CARD:
                case Payment::METHOD_PAYPAL:
                case Payment::METHOD_BANK_TRANSFER:
                    // For demo purposes, we'll simulate payment processing
                    // In a real app, you'd integrate with payment gateways here
                    $this->simulatePaymentProcessing($payment, $request);
                    break;
            }

            // Clear cart
            Cart::where('CustomerID', $customerId)->delete();

            DB::commit();

            return redirect()->route('checkout.success', $order->OrderID)
                ->with('success', 'Your order has been placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout failed: ' . $e->getMessage());
            
            return back()->withErrors([
                'checkout' => 'An error occurred while processing your order. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Show order success page
     */
    public function success($orderId)
    {
        $order = Order::with(['orderItems.product', 'payment', 'customer'])
            ->where('OrderID', $orderId)
            ->where('CustomerID', Auth::id())
            ->first();

        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        return view('customer.checkout.success', compact('order'));
    }

    /**
     * Simulate payment processing (for demo purposes)
     */
    private function simulatePaymentProcessing($payment, $request)
    {
        // In a real application, you would:
        // 1. Call payment gateway API (Stripe, PayPal, etc.)
        // 2. Handle webhooks for payment confirmation
        // 3. Update payment status based on gateway response
        
        // For demo, we'll simulate successful payment 90% of the time
        $isSuccessful = rand(1, 100) <= 90;
        
        if ($isSuccessful) {
            $payment->update([
                'payment_status' => Payment::STATUS_COMPLETED,
                'paid_at' => now(),
                'transaction_id' => 'sim_' . uniqid(),
                'payment_details' => [
                    'simulated' => true,
                    'processed_at' => now()->toISOString(),
                    'method_details' => $this->getPaymentMethodDetails($payment->payment_method)
                ]
            ]);
            
            // Update order status
            $payment->order->update(['Status' => Order::STATUS_PROCESSING]);
        } else {
            $payment->update([
                'payment_status' => Payment::STATUS_FAILED,
                'payment_details' => [
                    'simulated' => true,
                    'error' => 'Simulated payment failure',
                    'failed_at' => now()->toISOString()
                ]
            ]);
        }
    }

    /**
     * Get payment method details for simulation
     */
    private function getPaymentMethodDetails($method)
    {
        switch ($method) {
            case Payment::METHOD_CREDIT_CARD:
            case Payment::METHOD_DEBIT_CARD:
                return [
                    'card_type' => 'Visa',
                    'last_four' => '****1234',
                    'exp_month' => '12',
                    'exp_year' => '2025'
                ];
            case Payment::METHOD_PAYPAL:
                return [
                    'paypal_email' => 'user@example.com',
                    'transaction_type' => 'instant_payment'
                ];
            case Payment::METHOD_BANK_TRANSFER:
                return [
                    'bank_name' => 'Sample Bank',
                    'account_type' => 'checking',
                    'reference_number' => 'TXN' . uniqid()
                ];
            default:
                return [];
        }
    }
}
