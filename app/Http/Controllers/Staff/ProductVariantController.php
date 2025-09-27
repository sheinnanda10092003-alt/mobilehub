<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Display variants for a specific product
     */
    public function index($productId)
    {
        $product = Product::with(['variants' => function($query) {
            $query->orderBy('Color')->orderBy('RAM')->orderBy('Storage');
        }])->findOrFail($productId);
        
        return view('staff.products.variants.index', compact('product'));
    }
    
    /**
     * Show the form for creating a new variant
     */
    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        
        return view('staff.products.variants.create', compact('product'));
    }
    
    /**
     * Store a newly created variant
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $request->validate([
            'Color' => 'required|string|max:50',
            'RAM' => 'required|string|max:20',
            'Storage' => 'required|string|max:20',
            'Price' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'SKU' => 'required|string|max:100|unique:product_variants,SKU',
            'ImageURL' => 'nullable|url',
        ]);
        
        // Check if this variant combination already exists
        $existingVariant = ProductVariant::where('ProductID', $productId)
            ->where('Color', $request->Color)
            ->where('RAM', $request->RAM)
            ->where('Storage', $request->Storage)
            ->first();
            
        if ($existingVariant) {
            return back()->withErrors(['variant' => 'This variant combination already exists.'])
                        ->withInput();
        }
        
        ProductVariant::create([
            'ProductID' => $productId,
            'Color' => $request->Color,
            'RAM' => $request->RAM,
            'Storage' => $request->Storage,
            'Price' => $request->Price,
            'Stock' => $request->Stock,
            'SKU' => $request->SKU,
            'image_url' => $request->ImageURL,
            'is_active' => $request->has('IsActive')
        ]);
        
        return redirect()->route('staff.products.variants.index', $productId)
                        ->with('success', 'Product variant created successfully.');
    }
    
    /**
     * Show the form for editing a variant
     */
    public function edit($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('ProductID', $productId)->findOrFail($variantId);
        
        return view('staff.products.variants.edit', compact('product', 'variant'));
    }
    
    /**
     * Update the specified variant
     */
    public function update(Request $request, $productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('ProductID', $productId)->findOrFail($variantId);
        
        $request->validate([
            'Color' => 'required|string|max:50',
            'RAM' => 'required|string|max:20',
            'Storage' => 'required|string|max:20',
            'Price' => 'required|numeric|min:0',
            'Stock' => 'required|integer|min:0',
            'SKU' => 'required|string|max:100|unique:product_variants,SKU,' . $variantId . ',VariantID',
            'ImageURL' => 'nullable|url',
            'IsActive' => 'boolean'
        ]);
        
        // Check if this variant combination already exists (excluding current variant)
        $existingVariant = ProductVariant::where('ProductID', $productId)
            ->where('Color', $request->Color)
            ->where('RAM', $request->RAM)
            ->where('Storage', $request->Storage)
            ->where('VariantID', '!=', $variantId)
            ->first();
            
        if ($existingVariant) {
            return back()->withErrors(['variant' => 'This variant combination already exists.'])
                        ->withInput();
        }
        
        $variant->update([
            'Color' => $request->Color,
            'RAM' => $request->RAM,
            'Storage' => $request->Storage,
            'Price' => $request->Price,
            'Stock' => $request->Stock,
            'SKU' => $request->SKU,
            'image_url' => $request->ImageURL,
            'is_active' => $request->has('IsActive')
        ]);
        
        return redirect()->route('staff.products.variants.index', $productId)
                        ->with('success', 'Product variant updated successfully.');
    }
    
    /**
     * Remove the specified variant
     */
    public function destroy($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('ProductID', $productId)->findOrFail($variantId);
        
        $variant->delete();
        
        return redirect()->route('staff.products.variants.index', $productId)
                        ->with('success', 'Product variant deleted successfully.');
    }
    
    /**
     * Toggle variant active status
     */
    public function toggle($productId, $variantId)
    {
        $variant = ProductVariant::where('ProductID', $productId)->findOrFail($variantId);
        $variant->is_active = !$variant->is_active;
        $variant->save();
        
        $status = $variant->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('staff.products.variants.index', $productId)
                        ->with('success', "Product variant has been {$status} successfully.");
    }
}
