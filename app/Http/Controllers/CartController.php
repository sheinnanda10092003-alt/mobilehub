<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Display the cart
     */
    public function index()
    {
        $cartItems = Cart::getCartItemsForCustomer(Auth::id());
        $total = Cart::getCartTotalForCustomer(Auth::id());
        
        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $customerId = Auth::id();
        $quantity = $request->quantity;

        // Check if product already exists in cart
        $existingCartItem = Cart::where('CustomerID', $customerId)
            ->where('ProductID', $product->ProductID)
            ->first();

        if ($existingCartItem) {
            // Update quantity
            $newQuantity = $existingCartItem->quantity + $quantity;
            
            if ($newQuantity > $product->Stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $product->Stock . ' items in stock.'
                ], 400);
            }

            $existingCartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            Cart::create([
                'CustomerID' => $customerId,
                'ProductID' => $product->ProductID,
                'quantity' => $quantity,
                'price' => $product->Price
            ]);
        }

        $cartCount = Cart::getCartCountForCustomer($customerId);
        $cartTotal = Cart::getCartTotalForCustomer($customerId);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $cartCount,
                'cart_total' => number_format($cartTotal, 2)
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Add product variant to cart
     */
    public function addVariant(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,VariantID',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        
        // Additional validation for stock
        if ($request->quantity > $variant->Stock) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $variant->Stock . ' items in stock.'
                ], 400);
            }
            return redirect()->back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        $customerId = Auth::id();
        $quantity = $request->quantity;

        // Check if variant already exists in cart
        $existingCartItem = Cart::where('CustomerID', $customerId)
            ->where('VariantID', $variant->VariantID)
            ->first();

        if ($existingCartItem) {
            // Update quantity
            $newQuantity = $existingCartItem->Quantity + $quantity;
            
            if ($newQuantity > $variant->Stock) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available. Only ' . $variant->Stock . ' items in stock.'
                    ], 400);
                }
                return redirect()->back()->withErrors(['quantity' => 'Not enough stock available.']);
            }

            $existingCartItem->update(['Quantity' => $newQuantity]);
        } else {
            // Create new cart item with variant
            Cart::create([
                'CustomerID' => $customerId,
                'ProductID' => $variant->ProductID, // Still reference the base product
                'VariantID' => $variant->VariantID, // Reference the specific variant
                'Quantity' => $quantity,
                'Price' => $variant->Price // Use variant price
            ]);
        }

        $cartCount = Cart::getCartCountForCustomer($customerId);
        $cartTotal = Cart::getCartTotalForCustomer($customerId);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product variant added to cart successfully!',
                'cart_count' => $cartCount,
                'cart_total' => number_format($cartTotal, 2)
            ]);
        }

        return redirect()->back()->with('success', 'Product variant added to cart successfully!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cart)
    {
        // Ensure the cart item belongs to the authenticated customer
        if ($cart->CustomerID !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->product->Stock
        ]);

        $cart->update(['quantity' => $request->quantity]);

        $cartCount = Cart::getCartCountForCustomer(Auth::id());
        $cartTotal = Cart::getCartTotalForCustomer(Auth::id());
        $itemTotal = $cart->total;

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'cart_count' => $cartCount,
                'cart_total' => number_format($cartTotal, 2),
                'item_total' => number_format($itemTotal, 2)
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove item from cart
     */
    public function remove(Cart $cart)
    {
        // Ensure the cart item belongs to the authenticated customer
        if ($cart->CustomerID !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        $cartCount = Cart::getCartCountForCustomer(Auth::id());
        $cartTotal = Cart::getCartTotalForCustomer(Auth::id());

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart!',
                'cart_count' => $cartCount,
                'cart_total' => number_format($cartTotal, 2)
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        Cart::where('CustomerID', Auth::id())->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!',
                'cart_count' => 0,
                'cart_total' => '0.00'
            ]);
        }

        return redirect()->back()->with('success', 'Cart cleared successfully!');
    }

    /**
     * Get cart count (for AJAX requests)
     */
    public function getCartCount()
    {
        $count = Auth::check() ? Cart::getCartCountForCustomer(Auth::id()) : 0;
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get cart items for sidebar/modal
     */
    public function getCartItems()
    {
        if (!Auth::check()) {
            return response()->json(['items' => [], 'total' => 0, 'count' => 0]);
        }

        $items = Cart::getCartItemsForCustomer(Auth::id());
        $total = Cart::getCartTotalForCustomer(Auth::id());
        $count = Cart::getCartCountForCustomer(Auth::id());

        return response()->json([
            'items' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->Name,
                    'price' => number_format($item->price, 2),
                    'quantity' => $item->quantity,
                    'total' => number_format($item->total, 2),
                    'image' => $item->product->image_url,
                    'url' => route('products.show', $item->product->ProductID)
                ];
            }),
            'total' => number_format($total, 2),
            'count' => $count
        ]);
    }
}
