<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $primaryKey = 'VariantID';
    
    protected $fillable = [
        'ProductID',
        'Color',
        'RAM',
        'Storage',
        'Price',
        'Stock',
        'SKU',
        'image_url',
        'is_active'
    ];
    
    protected $casts = [
        'Price' => 'decimal:2',
        'Stock' => 'integer',
        'is_active' => 'boolean'
    ];
    
    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
    
    // Relationship with OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'VariantID');
    }
    
    // Relationship with Cart
    public function carts()
    {
        return $this->hasMany(Cart::class, 'VariantID');
    }
    
    // Generate SKU automatically
    public static function generateSKU($productId, $color, $ram, $storage)
    {
        $product = Product::find($productId);
        $productName = $product ? substr($product->Name, 0, 3) : 'PRD';
        
        return strtoupper($productName . '-' . substr($color, 0, 3) . '-' . $ram . '-' . $storage . '-' . time());
    }
    
    // Get formatted variant name
    public function getVariantNameAttribute()
    {
        return $this->product->Name . ' (' . $this->Color . ', ' . $this->RAM . ', ' . $this->Storage . ')';
    }
    
    // Check if variant is in stock
    public function isInStock()
    {
        return $this->Stock > 0;
    }
    
    // Check if variant is low stock
    public function isLowStock($threshold = 10)
    {
        return $this->Stock <= $threshold && $this->Stock > 0;
    }
    
    // Get stock status
    public function getStockStatusAttribute()
    {
        if ($this->Stock == 0) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
}
