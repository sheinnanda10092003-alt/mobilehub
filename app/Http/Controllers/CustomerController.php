<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display the customer homepage with optional search
     */
    public function homepage(Request $request)
    {
        $search = $request->get('search');
        
        // Base query with active variants
        $query = Product::with(['variants' => function ($q) {
            $q->where('is_active', true);
        }, 'supplier']);
        
        // If search is provided, filter products
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                  ->orWhere('Description', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', function($subQ) use ($search) {
                      $subQ->where('Name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('variants', function($subQ) use ($search) {
                      $subQ->where('Color', 'LIKE', "%{$search}%")
                           ->orWhere('RAM', 'LIKE', "%{$search}%")
                           ->orWhere('Storage', 'LIKE', "%{$search}%")
                           ->orWhere('SKU', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Get products with pagination
        $phones = $query->paginate(12);
        
        // Append search parameter to pagination links
        if ($search) {
            $phones->appends(['search' => $search]);
        }
        
        // Get search stats for display
        $searchStats = null;
        if ($search) {
            $totalProducts = Product::count();
            $foundProducts = $phones->total();
            $searchStats = [
                'query' => $search,
                'found' => $foundProducts,
                'total' => $totalProducts
            ];
        }
        
        return view('customer.homepage', compact('phones', 'search', 'searchStats'));
    }
    
    /**
     * AJAX search suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $search = $request->get('q');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        
        // Get product suggestions
        $products = Product::where('Name', 'LIKE', "%{$search}%")
                          ->orWhere('Description', 'LIKE', "%{$search}%")
                          ->limit(5)
                          ->get(['ProductID', 'Name', 'image_url']);
        
        // Get brand suggestions from suppliers
        $brands = Product::join('suppliers', 'products.SupplierID', '=', 'suppliers.SupplierID')
                        ->where('suppliers.Name', 'LIKE', "%{$search}%")
                        ->distinct()
                        ->limit(3)
                        ->pluck('suppliers.Name');
        
        // Get variant suggestions (colors, RAM, storage)
        $variants = DB::table('product_variants')
                     ->where('is_active', true)
                     ->where(function($q) use ($search) {
                         $q->where('Color', 'LIKE', "%{$search}%")
                           ->orWhere('RAM', 'LIKE', "%{$search}%")
                           ->orWhere('Storage', 'LIKE', "%{$search}%");
                     })
                     ->select('Color', 'RAM', 'Storage')
                     ->distinct()
                     ->limit(3)
                     ->get();
        
        $suggestions = [];
        
        // Add product suggestions
        foreach ($products as $product) {
            $suggestions[] = [
                'type' => 'product',
                'text' => $product->Name,
                'image' => $product->image_url
            ];
        }
        
        // Add brand suggestions
        foreach ($brands as $brand) {
            $suggestions[] = [
                'type' => 'brand',
                'text' => $brand
            ];
        }
        
        // Add variant suggestions
        foreach ($variants as $variant) {
            if (stripos($variant->Color, $search) !== false) {
                $suggestions[] = [
                    'type' => 'color',
                    'text' => $variant->Color
                ];
            }
            if (stripos($variant->RAM, $search) !== false) {
                $suggestions[] = [
                    'type' => 'spec',
                    'text' => $variant->RAM . ' RAM'
                ];
            }
            if (stripos($variant->Storage, $search) !== false) {
                $suggestions[] = [
                    'type' => 'spec',
                    'text' => $variant->Storage . ' Storage'
                ];
            }
        }
        
        return response()->json(array_slice($suggestions, 0, 8));
    }
}
