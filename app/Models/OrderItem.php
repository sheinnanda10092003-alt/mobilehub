<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //protected $primaryKey = 'OrderItemID';

    protected $fillable = [
        'OrderID',
        'ProductID',
        'VariantID',
        'Quantity',
        'UnitPrice',
    ];

    protected $casts = [
        'UnitPrice' => 'decimal:2',
        'Quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
    
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'VariantID');
    }
}
