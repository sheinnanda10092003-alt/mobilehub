<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // List all products
    public function index()
    {
        $products = Product::with(['supplier', 'variants'])->get();
        return view('staff.products.index', compact('products'));
    }

    // Show create product form
    public function create()
    {
        $suppliers = Supplier::all();
        return view('staff.products.create', compact('suppliers'));
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Price' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'image_url' => 'nullable|url',
        ]);

        Product::create([
            'Name' => $request->Name,
            'Description' => $request->Description,
            'Price' => $request->Price,
            'Stock' => $request->Stock,
            'SupplierID' => $request->SupplierID,
            'image_url' => $request->image_url,
        ]);

        return redirect()->route('staff.products.index')->with('success', 'Product created successfully!');
    }

    // Show single product
    public function show($id)
    {
        $product = Product::with(['supplier', 'inventories', 'orderItems.order'])->findOrFail($id);
        return view('staff.products.show', compact('product'));
    }

    // Show single product edit form
    public function edit($id)
    {
        $product = Product::with('supplier')->findOrFail($id);
        $suppliers = Supplier::all();
        return view('staff.products.edit', compact('product', 'suppliers'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'Name' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Price' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'image_url' => 'nullable|url',
        ]);

        $product->update([
            'Name' => $request->Name,
            'Description' => $request->Description,
            'Price' => $request->Price,
            'Stock' => $request->Stock,
            'SupplierID' => $request->SupplierID,
            'image_url' => $request->image_url,
        ]);

        return redirect()->route('staff.products.show', $product->ProductID)->with('success', 'Product updated successfully!');
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->Name;
        
        $product->delete();

        return redirect()->route('staff.products.index')->with('success', "Product '{$productName}' deleted successfully!");
    }
}

