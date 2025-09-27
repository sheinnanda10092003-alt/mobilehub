<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'CustomerID',
        'ProductID',
        'VariantID',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
    
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'VariantID');
    }

    // Helper methods
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public static function getCartItemsForCustomer($customerId)
    {
        return self::with(['product', 'variant'])
            ->where('CustomerID', $customerId)
            ->get();
    }

    public static function getCartTotalForCustomer($customerId)
    {
        return self::where('CustomerID', $customerId)
            ->get()
            ->sum(function($item) {
                return $item->price * $item->quantity;
            });
    }

    public static function getCartCountForCustomer($customerId)
    {
        return self::where('CustomerID', $customerId)
            ->sum('quantity');
    }
}
