<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'creator', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('staff.purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::with('supplier')->orderBy('Name')->get();
        
        return view('staff.purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,SupplierID',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,ProductID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Create purchase
            $purchase = Purchase::create([
                'SupplierID' => $request->supplier_id,
                'TotalAmount' => $totalAmount,
                'PurchaseDate' => $request->purchase_date,
                'Notes' => $request->notes,
                'CreatedBy' => Auth::guard('staff')->id(),
                'Status' => 'pending',
            ]);

            // Create purchase items
            foreach ($request->items as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                
                PurchaseItem::create([
                    'PurchaseID' => $purchase->PurchaseID,
                    'ProductID' => $item['product_id'],
                    'Quantity' => $item['quantity'],
                    'UnitPrice' => $item['unit_price'],
                    'TotalPrice' => $totalPrice,
                ]);
            }
        });

        return redirect()->route('staff.purchases.index')
            ->with('success', 'Purchase order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'creator', 'items.product'])
            ->findOrFail($id);
            
        return view('staff.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $purchase = Purchase::with(['items.product'])->findOrFail($id);
        
        // Only allow editing of pending purchases
        if (!$purchase->isPending()) {
            return redirect()->route('staff.purchases.show', $id)
                ->with('error', 'Only pending purchases can be edited.');
        }
        
        $suppliers = Supplier::all();
        $products = Product::with('supplier')->orderBy('Name')->get();
        
        return view('staff.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        
        // Only allow updating pending purchases
        if (!$purchase->isPending()) {
            return redirect()->route('staff.purchases.show', $id)
                ->with('error', 'Only pending purchases can be updated.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,SupplierID',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,ProductID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Update purchase
            $purchase->update([
                'SupplierID' => $request->supplier_id,
                'TotalAmount' => $totalAmount,
                'PurchaseDate' => $request->purchase_date,
                'Notes' => $request->notes,
            ]);

            // Delete existing items
            $purchase->items()->delete();

            // Create new purchase items
            foreach ($request->items as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                
                PurchaseItem::create([
                    'PurchaseID' => $purchase->PurchaseID,
                    'ProductID' => $item['product_id'],
                    'Quantity' => $item['quantity'],
                    'UnitPrice' => $item['unit_price'],
                    'TotalPrice' => $totalPrice,
                ]);
            }
        });

        return redirect()->route('staff.purchases.show', $id)
            ->with('success', 'Purchase order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        
        // Only allow deleting pending purchases
        if (!$purchase->isPending()) {
            return redirect()->route('staff.purchases.index')
                ->with('error', 'Only pending purchases can be deleted.');
        }
        
        $purchase->delete();
        
        return redirect()->route('staff.purchases.index')
            ->with('success', 'Purchase order deleted successfully!');
    }

    /**
     * Update purchase status to received and update stock
     */
    public function receive($id)
    {
        $purchase = Purchase::with('items.product')->findOrFail($id);
        
        if (!$purchase->isPending()) {
            return redirect()->route('staff.purchases.show', $id)
                ->with('error', 'Only pending purchases can be received.');
        }

        DB::transaction(function () use ($purchase) {
            // Update purchase status
            $purchase->update(['Status' => 'received']);
            
            // Update product stock
            foreach ($purchase->items as $item) {
                $product = $item->product;
                $product->increment('Stock', $item->Quantity);
            }
        });

        return redirect()->route('staff.purchases.show', $id)
            ->with('success', 'Purchase received successfully! Stock has been updated.');
    }

    /**
     * Cancel a purchase
     */
    public function cancel($id)
    {
        $purchase = Purchase::findOrFail($id);
        
        if (!$purchase->isPending()) {
            return redirect()->route('staff.purchases.show', $id)
                ->with('error', 'Only pending purchases can be cancelled.');
        }
        
        $purchase->update(['Status' => 'cancelled']);
        
        return redirect()->route('staff.purchases.show', $id)
            ->with('success', 'Purchase cancelled successfully!');
    }
}
